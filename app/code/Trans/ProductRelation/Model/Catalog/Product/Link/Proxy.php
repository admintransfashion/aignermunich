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

namespace Trans\ProductRelation\Model\Catalog\Product\Link;

/**
 * Class Proxy
 */
class Proxy extends \Magento\Catalog\Model\Product\Link\Proxy
{
    /**
     * {@inheritdoc}
     */
    public function useCombinationsLinks()
    {
        return $this->_getSubject()->useCombinationsLinks();
    }
}