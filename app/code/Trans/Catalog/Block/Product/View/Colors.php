<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Catalog
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Catalog\Block\Product\View;

use Magento\Framework\Exception\NoSuchEntityException;

class Colors extends \Magento\Catalog\Block\Product\AbstractProduct
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollection;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->productCollection = $productCollection;

        parent::__construct($context, $data);
    }

    /**
     * get items
     *
     * @return array
     */
    public function getItems()
    {
        $items = [];
        $currentProduct = $this->getProduct();
        $articleGroup = $currentProduct->getResource()->getAttribute('article_group')->getFrontend()->getValue($currentProduct);
        if($articleGroup) {
            $collection = $this->productCollection->create();
            $collection->addAttributeToSelect('*');
            $collection->addFieldToFilter('entity_id', ['neq' => $currentProduct->getId()]);
            $collection->addFieldToFilter('article_group', $articleGroup);
            $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            if($collection && count($collection->getData()) > 0){
                foreach($collection->getData() as $item){
                    $product = $this->getProductData($item['entity_id']);
                    if(!$product) {
                        continue;
                    }
                    $items[] = $product;
                }
            }
        }

        return $items;
    }

    /**
     * get product by id
     *
     * @param int $productId
     * @return Magento\Catalog\Api\Data\ProductInterface|bool
     */
    public function getProductData(int $productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
