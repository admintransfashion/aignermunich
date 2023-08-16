<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Reservation
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Reservation\Api;

use Magento\Framework\Exception\InputException;

/**
 * Interface for product helper.
 * @api
 */
interface ProductHelperInterface
{
    /**
     * get product id by attributes.
     *
     * @param array $attributeInfo
     * @param int $parentId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildProductIdByAttribute(array $attributeInfo, int $parentId);

}
