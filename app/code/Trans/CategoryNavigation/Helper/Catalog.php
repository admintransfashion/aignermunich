<?php
/**
 * @category Trans
 * @package  Trans_CategoryNavigation
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CategoryNavigation\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Catalog
 */
class Catalog extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollection;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    )
    {
        parent::__construct($context);
        $this->productCollection = $productCollection;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get config value by path
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * check category product is in stock and active
     *
     * @param int $categoryId
     * @return bool
     */
    public function checkCategoryProduct(int $categoryId)
    {
        if (!isset($this->instances[$categoryId])) {
            $result = false;
//            $stockIdActive = $this->integrationAssignSources->getStockIdActive();
            $stockIdActive = 2;
            $inventoryTable = 'inventory_stock_' . $stockIdActive;

            try {
                $category = $this->categoryRepository->get($categoryId);
                $products = $category->getProductCollection()->addAttributeToFilter('status', ['eq' => 1]);
                $products->getSelect()->join(['stock' => $inventoryTable], 'e.sku = stock.sku', ['stock.quantity']);

                if($products->getSize() > 0) {
                    $result = true;
                }
            } catch (NoSuchEntityException $e) {
                $result = false;
            }

            $this->instances[$categoryId] = $result;
        }

        return $this->instances[$categoryId];
    }
}
