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
 * Class SaleProduct
 * @package CTCD\ProductList\Block\Product
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SaleProduct extends \CTCD\ProductList\Block\Product\ProductList
{
    const DEFAULT_CHILD_URL = 'sale.html';

    /**
     * @return Collection
     */
    protected function initializeProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFinalPrice();
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->getSelect()->where('price_index.final_price < price_index.price');
        $this->addToolbarBlock($collection);
        return $collection;
    }
}
