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

namespace Trans\Reservation\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;

/**
 * Class Cancel
 */
class Cancel extends Action
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Helper\DataHelper
     */
    protected $dataHelper;

    /**
     * @var \Trans\Core\Helper\Email
     */
    protected $emailHelper;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationHelper;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * View constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Trans\Core\Helper\Email $emailHelper
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Customer\Model\Session $customerSession,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Core\Helper\Email $emailHelper,
        \Trans\Reservation\Helper\Reservation $reservationHelper
    )
    {
        $this->customerSession = $customerSession;
        $this->timezone = $timezone;
        $this->dataHelper = $dataHelper;
        $this->emailHelper = $emailHelper;
        $this->reservationHelper = $reservationHelper;
        $this->reservationRepository = $reservationRepository;
        $this->logger = $this->dataHelper->getLogger();
        $this->storeManager = $this->dataHelper->getStoreManager();

        parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $this->logger->info('========= Cancel Reservation by Customer Start ==========');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if(!$this->dataHelper->isCustomerLoggedIn()) {
            $this->messageManager->addErrorMessage(__('You have to login or register first.'));
            return $resultRedirect->setPath('*/');
        }

        $reservationId = $this->getRequest()->getParam('id');

        try {
            $data = $this->reservationRepository->getById($reservationId);

            if($data->getFlag() == ReservationInterface::FLAG_CANCEL) {
                $this->logger->info('Reservation already deleted.');
                $this->messageManager->addErrorMessage(__('Reservation data already deleted.'));
                $resultRedirect->setPath($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }

            if($data->getFlag() == ReservationInterface::FLAG_FINISH) {
                $this->logger->info(__('Reservation data number %1 is not cancelable.', $data->getReservationNumber()));
                $this->messageManager->addErrorMessage(__('Reservation data with number %1 is not cancelable.', $data->getReservationNumber()));
                $resultRedirect->setPath($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } catch (NoSuchEntityException $e) {
            // saved in var/log/reservation.log
            $this->logger->info('Error cancel reservation. Message = ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Cancel reservation failed, reservation data not found.'));
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        } catch (\Exception $e) {
            // saved in var/log/reservation.log
            $this->logger->info('Error cancel reservation. Message = ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Cancel reservation failed, a system error occurred.'));
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $data->setFlag(ReservationInterface::FLAG_CANCEL);

        try {
            $this->logger->info('cancel reservation number = ' . $data->getReservationNumber() . ' success');
            $this->update($data);
            $this->messageManager->addSuccessMessage(__('Cancel reservation success.'));

            /* dispatch */
            $this->_eventManager->dispatch('cancel_reservation_success', ['reservation_id' => $reservationId]);

            /* send email */
            $this->sendEmailNotif($data);
        } catch (\Exception $e) {
            $this->logger->info('cancel reservation number = ' . $data->getReservationNumber() . ' failed. Message = ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Cancel reservation failed, a system error occurred.'));
        }

        $resultRedirect->setPath($this->_redirect->getRefererUrl());
        $this->logger->info('========= Cancel Reservation by Customer End ==========');
        return $resultRedirect;
    }

    /**
     * Process update reservation data
     *
     * @param Trans\Reservation\Data\Api\ReservationInterface $reservation
     * @return void
     */
    protected function update($reservation)
    {
        $this->reservationRepository->save($reservation);
        $this->reservationHelper->releaseReservationItems($reservation);
        $this->reservationHelper->updateReservationItemsByReservationId($reservation->getId(), ReservationItemInterface::FLAG_CANCEL);
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
            $customer = $this->customerSession->getCustomer();
            $emailTo = $customer->getEmail();
            $template = 'cancel_reservation_notification';
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
