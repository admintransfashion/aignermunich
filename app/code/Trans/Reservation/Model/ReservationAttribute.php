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

use Trans\Reservation\Api\Data\ReservationAttributeInterface;
use Trans\Reservation\Model\ResourceModel\ReservationAttribute as ResourceModel;

/**
 * Class ReservationAttribute
 */
class ReservationAttribute extends \Magento\Framework\Model\AbstractModel implements ReservationAttributeInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'trans_reservation_attribute';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_reservation_attribute';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_reservation_attribute';

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
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getReservationId()
    {
        return $this->getData(ReservationAttributeInterface::RESERVATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setReservationId($reservationId)
    {
        $this->setData(ReservationAttributeInterface::RESERVATION_ID, $reservationId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute()
    {
        return $this->getData(ReservationAttributeInterface::ATTRIBUTE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($attribute)
    {
        $this->setData(ReservationAttributeInterface::ATTRIBUTE, $attribute);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData(ReservationAttributeInterface::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->setData(ReservationAttributeInterface::VALUE, $value);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFlag()
    {
        return $this->getData(ReservationAttributeInterface::FLAG);
    }

    /**
     * {@inheritdoc}
     */
    public function setFlag($flag)
    {
        $this->setData(ReservationAttributeInterface::FLAG, $flag);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(ReservationAttributeInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($datetime)
    {
        $this->setData(ReservationAttributeInterface::CREATED_AT, $datetime);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt() {
        return $this->getData(ReservationAttributeInterface::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($datetime)
    {
        $this->setData(ReservationAttributeInterface::UPDATED_AT, $datetime);
        return $this;
    }


}
