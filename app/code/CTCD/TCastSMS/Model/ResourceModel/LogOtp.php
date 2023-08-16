<?php
/**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_TCastSMS
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\TCastSMS\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\AbstractModel;
use CTCD\TCastSMS\Api\Data\LogOtpInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class LogOtp extends AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * Construct
     *
     * @param Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param string $connectionName
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     *
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        $connectionName = null
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LogOtpInterface::TABLE_NAME, LogOtpInterface::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $utcDate = $this->dateTime->gmtDate('Y-m-d H:i:s');

        if ($object->isObjectNew()) {
            $object->setCreatedAt($utcDate);
        }

        $object->setUpdatedAt($utcDate);

        return parent::_beforeSave($object);
    }

    /**
     * Get log identifier by verification Id
     *
     * @param string $verificationId
     * @return int|false
     */
    public function getIdByKey($verificationId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(LogOtpInterface::TABLE_NAME, LogOtpInterface::ENTITY_ID)->where(LogOtpInterface::VERIFICATION_ID .' = :verificationId');

        $bind = [':verificationId' => (string) $verificationId];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * Count telephone by Today
     *
     * @param string $mobileNumber
     * @return int
     */
    public function countTelephoneByToday($mobileNumber)
    {
        $currentDate = $this->dateTime->gmtDate('Y-m-d');
        $startDate = $currentDate . ' 00:00:00';
        $endDate = $currentDate . ' 23:59:59';

        $connection = $this->getConnection();

        $select = $connection->select()
                             ->from(LogOtpInterface::TABLE_NAME, LogOtpInterface::ENTITY_ID)
                             ->where(LogOtpInterface::MOBILE_NUMBER .' = :mobileNumber')
                             ->where(LogOtpInterface::DELIVERED .' <> :delivered')
                             ->where(LogOtpInterface::CREATED_AT .' >= :startDate')
                             ->where(LogOtpInterface::CREATED_AT .' <= :endDate');

        $bind = [
            'mobileNumber' => (string) $mobileNumber,
            'delivered' => (int) LogOtpInterface::STATUS_FAILED,
            'startDate' => (string) $startDate,
            'endDate' => (string) $endDate
        ];

        $result = $connection->fetchAll($select, $bind);

        return (int) count($result);
    }

    /**
     * @return string
     */
    public function getLinkField()
    {
        $connection = $this->getConnection();
        $indexList = $connection->getIndexList(LogOtpInterface::TABLE_NAME);
        return $indexList[$connection->getPrimaryKeyName(LogOtpInterface::TABLE_NAME)]['COLUMNS_LIST'][0];
    }
}
