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

namespace Trans\Reservation\Model;

use Trans\Reservation\Api\Data\UserStoreInterface;
use Trans\Reservation\Model\ResourceModel\UserStore as ResourceModel;

/**
 * Class UserStore
 */
class UserStore extends \Magento\Framework\Model\AbstractModel implements UserStoreInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'trans_user_store';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_user_store';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_user_store';

    /**
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(UserStoreInterface::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($dataId)
    {
        return $this->setData(UserStoreInterface::ID, $dataId);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->getData(UserStoreInterface::USER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId($userId)
    {
        return $this->setData(UserStoreInterface::USER_ID, $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreCode()
    {
        return $this->getData(UserStoreInterface::STORE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreCode($code)
    {
        return $this->setData(UserStoreInterface::STORE_CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedBy()
    {
        return $this->getData(UserStoreInterface::CREATED_BY);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedBy($userId)
    {
        return $this->setData(UserStoreInterface::CREATED_BY, $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedBy() {
        return $this->getData(UserStoreInterface::UPDATED_BY);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedBy($userId)
    {
        return $this->setData(UserStoreInterface::UPDATED_BY, $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(UserStoreInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created)
    {
        return $this->setData(UserStoreInterface::CREATED_AT, $created);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt() {
        return $this->getData(UserStoreInterface::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(UserStoreInterface::UPDATED_AT, $updatedAt);
    }
}
