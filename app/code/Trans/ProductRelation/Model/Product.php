<?php
/**
 * @category Trans
 * @package  Trans_ProductRelation
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\ProductRelation\Model;

/**
 * Class combinations
 */
class Product extends \Magento\Catalog\Model\Product
{
    /**
     * Retrieve array of Combinations products
     *
     * @return array
     */
    public function getCombinationsProducts() 
    {
        if (!$this->hasCombinationsProducts()) {
            $products = [];
            foreach ($this->getCombinationsProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setCombinationsProducts($products);
        }
        return $this->getData('combinations_products');
    }
    /**
     * Retrieve Combinations products identifiers
     *
     * @return array
     */
    public function getCombinationsIds() 
    {
        if (!$this->hasCombinationsProductIds()) {
            $ids = [];
            foreach ($this->getCombinationsProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setCombinationsProductIds($ids);
        }
        return $this->getData('combinations_product_ids');
    }
    /**
     * Retrieve collection Combinations product
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getCombinationsProductCollection() 
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection */
        $collection = $this->getLinkInstance()->useCombinationsLinks()->getProductCollection()->setIsStrongMode();
        $collection
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('special_price');
        $collection->setProduct($this);
        return $collection;
    }
    /**
     * Retrieve collection Combinations link
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Collection
     */
    public function getCombinationsLinkCollection() 
    {
        $collection = $this->getLinkInstance()->useCombinationsLinks()->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }
    
}