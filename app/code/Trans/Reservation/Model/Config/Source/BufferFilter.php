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

namespace Trans\Reservation\Model\Config\Source;

use Trans\Reservation\Api\Data\ReservationConfigInterface;

/**
 * Class BufferFilter
 */
class BufferFilter implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => ReservationConfigInterface::FILTER_CATEGORY, 'label' => __('Category')],
            ['value' => ReservationConfigInterface::FILTER_PRODUCT, 'label' => __('Product')],
            ['value' => ReservationConfigInterface::FILTER_STORE, 'label' => __('Store')],
            ['value' => ReservationConfigInterface::FILTER_ALL, 'label' => __('Category & Product')],
            // ['value' => ReservationConfigInterface::FILTER_STORE, 'label' => __('Store')],
        ];
    }
}
