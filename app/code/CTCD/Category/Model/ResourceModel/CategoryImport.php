<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\AbstractModel;
use CTCD\Category\Api\Data\CategoryImportInterface;

/**
 * Class CategoryImport
 */
class CategoryImport extends AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
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
        \Magento\Backend\Model\Auth\Session $authSession,
        $connectionName = null
    ) {
        $this->dateTime = $dateTime;
        $this->authSession = $authSession;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(CategoryImportInterface::TABLE_NAME, CategoryImportInterface::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $user = $this->authSession->getUser();
        $utcDate = $this->dateTime->gmtDate('Y-m-d H:i:s');

        if ($object->isObjectNew()) {
            $object->setCreatedAt($utcDate);
            $object->setAdminId($user->getId());
        }

        $object->setUpdatedAt($utcDate);

        return parent::_beforeSave($object);
    }
}
