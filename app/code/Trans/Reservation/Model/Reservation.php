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

use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Model\ResourceModel\Reservation as ResourceModel;

/**
 * Class Reservation
 *
 * @SuppressWarnings(PHPMD)
 */
class Reservation extends \Magento\Framework\Model\AbstractModel implements ReservationInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'trans_reservation';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_reservation';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_reservation';

    /**
     * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
     */
    protected $reservationItemRepository;

    /**
     * @var \Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory
     */
    protected $itemCollection;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
     * @param \Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory $itemCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository,
        \Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory $itemCollection,
        array $data = []
    ) {
        $this->reservationItemRepository = $reservationItemRepository;
        $this->itemCollection = $itemCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_construct();
    }

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
    public function getCustomerId()
    {
        return (int) $this->getData(ReservationInterface::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId(int $customerId)
    {
        $this->setData(ReservationInterface::CUSTOMER_ID, $customerId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFlag()
    {
        return $this->getData(ReservationInterface::FLAG);
    }

    /**
     * {@inheritdoc}
     */
    public function setFlag($flag)
    {
        $this->setData(ReservationInterface::FLAG, $flag);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReservationNumber()
    {
        return $this->getData(ReservationInterface::RESERVATION_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function setReservationNumber($referenceNumber)
    {
        $this->setData(ReservationInterface::RESERVATION_NUMBER, $referenceNumber);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReservationDateSubmit()
    {
        return $this->getData(ReservationInterface::RESERVATION_DATE_SUBMIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setReservationDateSubmit($date)
    {
        $this->setData(ReservationInterface::RESERVATION_DATE_SUBMIT, $date);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(ReservationInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($datetime)
    {
        return $this->setData(ReservationInterface::CREATED_AT, $datetime);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt() {
        return $this->getData(ReservationInterface::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($datetime)
    {
        $this->setData(ReservationInterface::UPDATED_AT, $datetime);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->getId() ? $this->reservationItemRepository->getByReservationId($this->getId()) : $this->itemCollection->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        $flag = $this->getFlag();

        if($flag === ReservationInterface::FLAG_SUBMIT) {
            return __('Waiting for your coming');
        }

        if($flag === ReservationInterface::FLAG_FINISH) {
            return __('Finish');
        }

        return ucfirst($flag);
    }
}
