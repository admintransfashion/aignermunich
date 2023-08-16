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
 * Class EmailReminder
 */
class EmailReminder
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
     */
    protected $reserveItemRepository;

    /**
     * @var \Trans\Core\Helper\Email
     */
    protected $emailHelper;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * View constructor.
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory $itemCollectionFactory
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reserveItemRepository
     * @param \Trans\Core\Helper\Email $emailHelper
     * @param \Trans\Reservation\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory $itemCollectionFactory,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reserveItemRepository,
        \Trans\Core\Helper\Email $emailHelper,
        \Trans\Reservation\Helper\Data $dataHelper
    )
    {
        $this->timezone = $timezone;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->reserveItemRepository = $reserveItemRepository;
        $this->customerRepository = $customerRepository;
        $this->emailHelper = $emailHelper;
        $this->dataHelper = $dataHelper;
        $this->logger = $this->dataHelper->getLogger();
    }


    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $this->logger->info('========= Cron ' . __CLASS__ . ' Start ==========');

        $datenow = $this->timezone->date()->format('Y-m-d');

        $collection = $this->itemCollectionFactory->create();
        $collection->addFieldToSelect(ReservationItemInterface::ID);
        $collection->addFieldToSelect(ReservationItemInterface::RESERVATION_ID);
        $collection->addFieldToSelect(ReservationItemInterface::FLAG);
        $collection->addFieldToFilter('main_table.' . ReservationItemInterface::FLAG, ReservationInterface::FLAG_SUBMIT);
        $collection->addFieldToFilter('main_table.' . ReservationItemInterface::REMINDER_EMAIL, ReservationItemInterface::REMINDER_EMAIL_FALSE);
        $collection->addFieldToFilter('main_table.' . ReservationItemInterface::START_DATE, ['lt' => $datenow]);
        // $collection->getSelect()->group(ReservationItemInterface::RESERVATION_ID);
        $collection->getSelect()->group('res.' . ReservationInterface::CUSTOMER_ID);
        $collection->getSelect()->join(['res' => ReservationInterface::TABLE_NAME], 'main_table.' . ReservationItemInterface::RESERVATION_ID . '=res.' . ReservationInterface::ID, ['res.' . ReservationInterface::CUSTOMER_ID]);

        $collection->setPageSize(20);
        $data = $collection->load();

        foreach($data as $reserve) {
            $send = $this->sendEmailNotif($reserve);

            if($send) {
                $reserve->setReminderEmail(ReservationItemInterface::REMINDER_EMAIL_TRUE);

                $this->reserveItemRepository->save($reserve);
            }
        }

        $this->logger->info('========= Cron ' . __CLASS__ . ' End ==========');
    }

    /**
     * Send email notification
     *
     * @param string $emailTo
     * @param array $emptyProduct
     * @return bool
     */
    public function sendEmailNotif($reservation)
    {
        try {
            $customer = $this->customerRepository->getById($reservation->getCustomerId());
            $emailTo = $customer->getEmail();
            $template = 'reservation_reminder_notification';
            $var['customer_name'] = $customer->getFirstname();
            $this->emailHelper->sendEmail($emailTo, $var, $template);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
