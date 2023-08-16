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

namespace Trans\Reservation\Model\ResourceModel\ReservationAttribute;

use Trans\Reservation\Model\ReservationAttribute;
use Trans\Reservation\Api\Data\ReservationAttributeInterface;
use Trans\Reservation\Model\ResourceModel\ReservationAttribute as ResourceModel;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ReservationAttributeInterface::ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_reservation_attribute';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'trans_reservation_attribute';

    /**
     * Define resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(ReservationAttribute::class, ResourceModel::class);
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Select::GROUP);

        return $countSelect;
    }
}
