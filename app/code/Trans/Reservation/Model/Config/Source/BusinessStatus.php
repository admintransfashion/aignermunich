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

use Trans\Reservation\Api\Data\ReservationItemInterface;

/**
 * Class BusinessStatus
 */
class BusinessStatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => ReservationItemInterface::BUSINESS_STATUS_RESERVE, 'label' => 'Waiting'],
            ['value' => ReservationItemInterface::BUSINESS_STATUS_VISIT, 'label' => 'Visit'],
            ['value' => ReservationItemInterface::BUSINESS_STATUS_PURCHASE, 'label' => 'Made Purchase'],
            ['value' => ReservationItemInterface::BUSINESS_STATUS_CHANGE, 'label' => 'Change Product'],
            ['value' => ReservationItemInterface::BUSINESS_STATUS_CANCELED, 'label' => 'Canceled'],
            ['value' => ReservationItemInterface::BUSINESS_STATUS_VISIT_CANCELED, 'label' => 'Visited & Canceled']
        ];

        return $options;
    }
}
