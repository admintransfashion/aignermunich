<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_ProductList
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\ProductList\Block\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Class NewProduct
 * @package CTCD\ProductList\Block\Product
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class NewProduct extends \CTCD\ProductList\Block\Product\ProductList
{
    const DEFAULT_CHILD_URL = 'new-in.html';

    /**
     * @return Collection
     */
    protected function initializeProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('is_new', ['eq' => 1]);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

        if($this->productListHelper->isProductDisplayedByBadge()) {
            $collection->addAttributeToFilter('is_bestseller', ['eq' => 0]);
            $collection->addFinalPrice();
            $collection->getSelect()->where('price_index.final_price >= price_index.price');
        }

        $this->addToolbarBlock($collection);
        return $collection;
    }
}
