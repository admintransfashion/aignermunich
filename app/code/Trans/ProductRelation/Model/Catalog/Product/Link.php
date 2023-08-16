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

namespace Trans\ProductRelation\Model\Catalog\Product;

/**
 * Class Link
 */
class Link extends \Magento\Catalog\Model\Product\Link
{
    const LINK_TYPE_COMBINATIONS = 6;

    /**
     * @return \Magento\Catalog\Model\Product\Link $this
     */
    public function useCombinationsLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_COMBINATIONS);
        return $this;
    }

    /**
     * Save data for product relations
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product\Link
     */
    public function saveProductRelations($product)
    {
        parent::saveProductRelations($product);

        $data = $product->getCombinationsData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product->getId(), $data, self::LINK_TYPE_COMBINATIONS);
        }

        return $this;
    }
}
