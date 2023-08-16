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

namespace Trans\Reservation\Observer;

use Magento\Framework\Event\ObserverInterface;
use Trans\Reservation\Api\Data\ReservationInterface;

/**
 * Class CustomerLogin
 */
class CustomerLogin implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;

    /**
     * @var \Trans\Reservation\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Model\ReservationItem
     */
    protected $reservationItem;

    /**
     * @var \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory
     */
    protected $reserveCollection;

    /**
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Model\ReservationItem $reservationItem
     * @param \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory $reserveCollection
     */
    public function __construct(
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Model\ReservationItem $reservationItem,
        \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory $reserveCollection
    ) {
        $this->customerSession = $customerSession;
        $this->dataHelper = $dataHelper;
        $this->reservationItem = $reservationItem;
        $this->reservationRepository = $reservationRepository;
        $this->reserveCollection = $reserveCollection;

        $this->logger = $dataHelper->getLogger();
    }

    /**
     * add reservation id to customer session
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->logger->info('----------------------- add reservation session -------------------------');
        $session = $this->customerSession->create();
        $customer = $observer->getEvent()->getCustomer(); //Get customer object
        $customerId = $customer->getId();
        $reservation = $this->getCustomerReserve($customerId);

        $this->logger->info('$reservation->getId() = ' . $reservation->getId());
        if($reservation->getId()) {
            if($session->getReservationId() && ($reservation->getId() != $session->getReservationId())) {
                $mergeItems = $this->mergeItems($session->getReservationId(), $reservation->getId());
                if($mergeItems) {
                    // delete reservation old data
                    $this->reservationRepository->deleteById($session->getReservationId());
                }
            }

            $session->setReservationId($reservation->getId());
            $this->logger->info('session getReservationId() = ' . $session->getReservationId());
        } else {
            if ($session->getReservationId()) {
                $reservation = $this->reservationRepository->getById($session->getReservationId());
                $reservation->setCustomerId($customerId);
                $this->reservationRepository->save($reservation);
            }
        }

        $this->logger->info('----------------------- end add reservation session -------------------------');
    }

    /**
     * Get customer unfinished/new reservation
     *
     * @return Trans\Reservation\Api\Data\ReservationInterface
     */
    protected function getCustomerReserve($customerId)
    {
        $collection = $this->reserveCollection->create();

        $collection->addFieldToFilter(ReservationInterface::CUSTOMER_ID, $customerId);
        $collection->addFieldToFilter(ReservationInterface::FLAG, ReservationInterface::FLAG_NEW);

        $data = $collection->getLastItem();

        return $data;
    }

    /**
     * merge reservation items
     *
     * @param int $sessionResId
     * @param int $destResId
     * @return bool
     */
    protected function mergeItems(int $sessionResId, int $destResId)
    {
        $source = $this->reservationRepository->getById($sessionResId);
        $sourceItems = $source->getItems();

        $merge = $this->reservationItem->mergeItems($sourceItems, $destResId);

        return $merge;
    }
}
