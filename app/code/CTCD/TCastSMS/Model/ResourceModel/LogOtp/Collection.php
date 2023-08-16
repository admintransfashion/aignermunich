<?php
/**LogOtp
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_TCastSMS
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\TCastSMS\Model\ResourceModel\LogOtp;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DB\Select;
use Psr\Log\LoggerInterface;
use CTCD\TCastSMS\Api\Data\LogOtpInterface;
use CTCD\TCastSMS\Model\LogOtp as LogOtpModel;
use CTCD\TCastSMS\Model\ResourceModel\LogOtp as LogOtpResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'ctcd_tcastotplog_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'tcastotplog_collection';

    /**
     * constructor
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param null $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LogOtpModel::class, LogOtpResourceModel::class);
        $this->map['fields'][LogOtpInterface::ENTITY_ID] = 'main_table.'.LogOtpInterface::ENTITY_ID;
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by mobile number field
     *
     * @param string $mobileNumber
     * @return $this
     */
    public function addPhoneFilter($mobileNumber)
    {
        if ($mobileNumber) {
            $this->addFieldToFilter(LogOtpInterface::MOBILE_NUMBER, array('eq' => $mobileNumber));
        }

        return $this;
    }

    /**
     * Add order by
     *
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function orderBy($field, $direction = 'ASC')
    {
        if ($field && $direction) {
            $this->setOrder($field, $direction);
        }

        return $this;
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
