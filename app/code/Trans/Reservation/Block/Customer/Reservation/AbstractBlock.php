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

namespace Trans\Reservation\Block\Customer\Reservation;

use Trans\Reservation\Api\Data\ReservationInterface;
/**
 * Class AbstractBlock
 */
class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * get view url
     *
     * @param \Trans\Reservation\Api\Data\ReservationInterface $reserve
     * @return url
     */
    public function getViewUrl(\Trans\Reservation\Api\Data\ReservationInterface $reserve)
    {
        return $this->getUrl('reservation/customer/detail/', ['id' => $reserve->getId()]);
    }

    /**
     * get cancel url
     *
     * @param \Trans\Reservation\Api\Data\ReservationInterface | \Trans\Reservation\Api\Data\ReservationItemInterface $reserve
     * @return url
     */
    public function getCancelUrl($reserve)
    {
        return $this->getUrl('reservation/customer/cancel/', ['id' => $reserve->getId()]);
    }

    /**
     * get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/index');
    }

    /**
     * is reservation cancelable
     *
     * @param \Trans\Reservation\Api\Data\ReservationInterface | \Trans\Reservation\Api\Data\ReservationItemInterface $reservation
     * @return bool
     */
    public function isCancelable($reservation)
    {
        if($reservation->getFlag() != ReservationInterface::FLAG_CANCEL && $reservation->getFlag() != ReservationInterface::FLAG_FINISH) {
            return true;
        }

        return false;
    }

    /**
     * is reservation canceled
     *
     * @param \Trans\Reservation\Api\Data\ReservationInterface | \Trans\Reservation\Api\Data\ReservationItemInterface $reservation
     * @return bool
     */
    public function isCancelled($reservation)
    {
        if($reservation->getFlag() == ReservationInterface::FLAG_CANCEL) {
            return true;
        }

        return false;
    }
}
