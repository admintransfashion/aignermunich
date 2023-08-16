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

namespace Trans\Reservation\Model\ResourceModel\ReservationConfig\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use Trans\Reservation\Model\ResourceModel\ReservationConfig\Grid\Collection;
use Trans\Reservation\Api\Data\ReservationConfigInterface;

class MaxqtyCollection extends Collection
{
    /**
     * get add collection query
     *
     * @return this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addFieldToFilter(ReservationConfigInterface::CONFIG, ['eq' => ReservationConfigInterface::CONFIG_MAXQTY]);

        return $this;
    }
}
