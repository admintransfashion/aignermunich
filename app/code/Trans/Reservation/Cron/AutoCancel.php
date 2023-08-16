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

namespace Trans\Reservation\Cron;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AutoCancel
 */
class AutoCancel
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Trans\Reservation\Model\ResouceModel\Reservation\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Helper\DataHelper
     */
    protected $dataHelper;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationHelper;

    /**
     * @var \Trans\Core\Helper\Email
     */
    protected $emailHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Trans\Reservation\Model\ResouceModel\Reservation\CollectionFactory $collectionFactory
     * @param \Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory $itemCollectionFactory
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Core\Helper\Email $emailHelper
     * @param \Trans\Reservation\Helper\Reservation $reservationHelper
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory $collectionFactory,
        \Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory $itemCollectionFactory,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Core\Helper\Email $emailHelper,
        \Trans\Reservation\Helper\Reservation $reservationHelper
    )
    {
        $this->eventManager = $eventManager;
        $this->customerRepository = $customerRepository;
        $this->collectionFactory = $collectionFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->timezone = $timezone;
        $this->dataHelper = $dataHelper;
        $this->emailHelper = $emailHelper;
        $this->reservationHelper = $reservationHelper;
        $this->reservationRepository = $reservationRepository;
        $this->reservationItemRepository = $reservationItemRepository;
        $this->logger = $this->dataHelper->getLogger();
        $this->storeManager = $this->dataHelper->getStoreManager();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $this->logger->info('========= Cron Auto Cancel Start ==========');
        $reserveIds = [];
        $now = $this->timezone->date()->format('Y-m-d H:i:s');

        $datenow = $this->timezone->date()->format('Y-m-d');
        $timenow = $this->timezone->date()->format('H:i');

        // $collection = $this->collectionFactory->create();
        // $collection->addFieldToSelect(ReservationInterface::RESERVATION_NUMBER);
        // $collection->addFieldToFilter('main_table.' . ReservationInterface::FLAG, ReservationInterface::FLAG_SUBMIT);
        // $collection->getSelect()->join(array('items' => ReservationItemInterface::TABLE_NAME), 'main_table.' . ReservationInterface::ID . ' = items.' . ReservationItemInterface::RESERVATION_ID);
        // $collection->addFieldToFilter('items.' . ReservationItemInterface::END_DATE, ['lteq' => $datenow]);
        // $collection->addFieldToFilter('items.' . ReservationItemInterface::END_TIME, ['lteq' => $timenow]);
        // $collection->setPageSize(20);

        $collection = $this->itemCollectionFactory->create();
        $collection->addFieldToSelect(ReservationItemInterface::ID);
        $collection->addFieldToSelect(ReservationItemInterface::RESERVATION_ID);
        $collection->addFieldToSelect(ReservationItemInterface::FLAG);
        $collection->addFieldToFilter(ReservationItemInterface::FLAG, ReservationInterface::FLAG_SUBMIT);
        $collection->addFieldToFilter('main_table.' . ReservationItemInterface::AUTOCANCEL_EMAIL, ReservationItemInterface::AUTOCANCEL_EMAIL_FALSE);
        $collection->getSelect()->where('CONCAT(' . ReservationItemInterface::END_DATE . ', " ", ' . ReservationItemInterface::END_TIME . ') <= "' . $datenow  . ' ' . $timenow . '"');
        $collection->setPageSize(20);

        $this->logger->info('sql query = ' . $collection->getSelect());

        $data = $collection->load();

        foreach($data as $reserve) {
            if($reserve->getFlag() == ReservationItemInterface::FLAG_SUBMIT) {
                $reserveIds[] = $reserve->getReservationId();

                $reservationItem = $this->reservationItemRepository->getById($reserve->getId());

                try {
                    $this->update($reservationItem);
                    $this->logger->info('reservation number = ' . $reserve->getReservationNumber() . ' success');
                    $this->eventManager->dispatch('cancel_reservation_success', ['reservation_id' => $reserve->getReservationId()]);

                    $reserve->setFlag(ReservationItemInterface::FLAG_CANCEL);
                    $reserve->setAutocancelEmail(ReservationItemInterface::AUTOCANCEL_EMAIL_TRUE);
                    $reserve->setBusinessStatus(ReservationItemInterface::BUSINESS_STATUS_CANCELED);
                    $this->reservationItemRepository->save($reserve);
                } catch (\Exception $e) {
                    $this->logger->info('reservation number = ' . $reserve->getReservationNumber() . ' failed. Message = ' . $e->getMessage());
                    continue;
                }
            }
        }

        if(!empty($reserveIds)) {
            $this->updateReservation($reserveIds);
        }

        $this->logger->info('========= Cron Auto Cancel End ==========');
    }

    /**
     * update data reservation item stock
     *
     * @param \Trans\Reservation\Api\Data\ReservationItemInterface $reservationItem
     * @return void
     */
    protected function update(\Trans\Reservation\Api\Data\ReservationItemInterface $reservationItem)
    {
        $this->reservationHelper->releaseReservationItemStock($reservationItem);
    }

    /**
     * update data reservation
     *
     * @param array $reservationIds
     * @return void
     */
    protected function updateReservation(array $reservationIds)
    {
        $this->logger->info('--start run ' . __FUNCTION__);
        foreach($reservationIds as $reservationId) {
            try {
                $reservation = $this->reservationRepository->getById($reservationId);
                $items = $reservation->getItems();
                $countItems = count($items);
                $itemCancelled = [];
                foreach($items as $item) {
                    if($item->getFlag() == ReservationItemInterface::FLAG_CANCEL) {
                        $itemCancelled[] = $item->getId();
                    }
                }

                if((int)$countItems === count($itemCancelled)) {
                    $reservation->setFlag(ReservationInterface::FLAG_CANCEL);
                    $this->reservationRepository->save($reservation);
                    $this->logger->info('success update reservationId = ' . $reservation->getId());
                    $this->sendEmailNotif($reservation);
                }
            } catch (NoSuchEntityException $e) {
                $this->logger->info('error ' . $e->getMessage());
                continue;
            } catch (\Exception $e) {
                $this->logger->info('error ' . $e->getMessage());
                continue;
            }
        }

        $this->logger->info('--end run ' . __FUNCTION__);
    }

    /**
     * Send email notification
     *
     * @param string $emailTo
     * @param array $emptyProduct
     * @return void
     */
    public function sendEmailNotif($reservation)
    {
        try {
            // $source = $this->dataHelper->getSourceRepository()->get($reservation->getSourceCode());
            $customer = $this->customerRepository->getById($reservation->getCustomerId());
            $emailTo = $customer->getEmail();
            $template = 'autocancel_reservation_notification';
            $var['reservation'] = $reservation;
            $var['placed_date'] = $this->dataHelper->changeDateFormat($reservation->getReservationDateSubmit(), 'F d, Y');
            $var['customer'] = $customer;
            $var['base_url'] = $this->storeManager->getStore()->getBaseUrl();
            // $var['source'] = $source;
            $this->emailHelper->sendEmail($emailTo, $var, $template);
            // $this->sendCalendar();
        } catch (\Exception $e) {
            return;
        }
    }

}
