<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_CatalogProductList
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\CatalogProductList\Plugin\CatalogSearch\Model\ResourceModel\Fulltext;

class Collection
{
    const FILTERED_ATTRIBUTES = ['color', 'model_group', 'size'];

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * @var \Trans\Reservation\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Trans\Reservation\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Trans\Reservation\Helper\Config $configHelper
    ){
        $this->registry = $registry;
        $this->productCollection = $productCollection;
        $this->configHelper = $configHelper;
    }

    public function afterGetFacetedData(
        \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $subject,
        $result,
        $field
    ){
        if($result){
            $bufferStock = (int) $this->configHelper->getGlobalProductBuffer();
            if($this->configHelper->isEnabled() && $bufferStock > 0 && in_array($field, self::FILTERED_ATTRIBUTES)) {
                $category = $this->registry->registry('current_category');
                if ($category && $category->getId()) {
                    $tableStockAlias = 'inventory_stock_2_' . $field;
                    $collection = clone $this->productCollection;
                    $collection->addAttributeToSelect('*')
                        ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                        ->addCategoryFilter($category);

                    $collection->getSelect()->join(
                        [$tableStockAlias => $collection->getTable('inventory_stock_2')],
                        'e.sku = ' . $tableStockAlias . '.sku AND ' . $tableStockAlias . '.quantity > ' . $bufferStock,
                        []
                    );
                    $collection->load();
                    if ($collection) {
                        $filteredValues = [];
                        foreach ($collection as $product) {
                            $filteredValues[] = $product->getData($field);
                        }
                        $filteredValues = array_filter($filteredValues);
                        if ($filteredValues) {
                            $filteredValues = array_unique($filteredValues);
                            $oldValues = array_keys($result);
                            $diffValues = array_diff($oldValues, $filteredValues);
                            if ($diffValues) {
                                foreach ($diffValues as $v) {
                                    unset($result[$v]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }
}
