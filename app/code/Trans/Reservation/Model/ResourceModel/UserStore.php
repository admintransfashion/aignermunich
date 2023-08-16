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

namespace Trans\Reservation\Model\ResourceModel;

use Magento\Framework\Stdlib\DateTime\DateTime as LibDateTime;
use Magento\Framework\Model\AbstractModel;
use Trans\Reservation\Api\Data\UserStoreInterface;

/**
 * Class UserStore
 */
class UserStore extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var LibDateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * Construct
     *
     * @param Context $context
     * @param DateTime $date
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        LibDateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->date = $date;
        $this->timezone = $timezone;
        $this->authSession = $authSession;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(UserStoreInterface::TABLE_NAME, UserStoreInterface::ID);
    }

    /**
     * get current admin user
     *
     * @return \Magento\User\Api\Data\UserInterface
     */
    private function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    /**
     * save updated at
     * @SuppressWarnings(PHPMD)
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $user = $this->getCurrentUser();

        if ($object->isObjectNew()) {
            if (!$object->hasCreatedAt()) {
                $object->setCreatedAt($this->timezone->date());
            }

            if($user) {
                $object->setCreatedBy($user->getId());
            }
        }

        $object->setUpdatedAt($this->timezone->date());
        if($user) {
            $object->setUpdatedBy($user->getId());
        }

        return parent::_beforeSave($object);
    }
}
