<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\AbstractModel;
use CTCD\Inspiration\Api\Data\InspirationInterface;
use CTCD\Inspiration\Helper\Data as DataHelper;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Inspiration extends AbstractDb
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
        $this->_init(DataHelper::INSPIRATION_TABLE, InspirationInterface::ENTITY_ID);
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
     * Get template class identifier by code
     *
     * @param string $key
     * @return int|false
     */
    public function getIdByKey($key)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(DataHelper::INSPIRATION_TABLE, InspirationInterface::ENTITY_ID)->where(InspirationInterface::URL_KEY.' = :key');

        $bind = [':key' => (string) $key];

        return $connection->fetchOne($select, $bind);
    }

    /**
     * @return string
     */
    public function getLinkField()
    {
        $connection = $this->getConnection();
        $indexList = $connection->getIndexList(DataHelper::INSPIRATION_TABLE);
        return $indexList[$connection->getPrimaryKeyName(DataHelper::INSPIRATION_TABLE)]['COLUMNS_LIST'][0];
    }
}
