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
 * Class Cancelitem
 */
class Cancelitem extends Action
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
     */
    protected $reservationItemRepository;

    /**
     * @var \Trans\Reservation\Helper\DataHelper
     */
    protected $dataHelper;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * View constructor.
     * @param Context $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Helper\Reservation $reservationHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Reservation $reservationHelper
    )
    {
        $this->timezone = $timezone;
        $this->dataHelper = $dataHelper;
        $this->reservationHelper = $reservationHelper;
        $this->reservationRepository = $reservationRepository;
        $this->reservationItemRepository = $reservationItemRepository;
        $this->logger = $this->dataHelper->getLogger();

        parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $this->logger->info(' ');
        $this->logger->info('========= Cancel Reservation Item by Customer Start ==========');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if(!$this->dataHelper->isCustomerLoggedIn()) {
            $this->messageManager->addErrorMessage(__('You have to login or register first.'));
            return $resultRedirect->setPath('*/');
        }

        $itemId = $this->getRequest()->getParam('id');

        if(!$itemId) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        try {
            $data = $this->reservationItemRepository->getById($itemId);

            if($data->getFlag() == ReservationItemInterface::FLAG_CANCEL) {
                $this->logger->info('Reservation item already cancelled.');
                $this->messageManager->addErrorMessage(__('Reservation item data already cancelled.'));
                $resultRedirect->setPath($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }

            if($data->getFlag() == ReservationItemInterface::FLAG_CONFIRM) {
                $this->logger->info(__('Reservation Item data number %1 is not cancelable.', $data->getReservationNumber()));
                $this->messageManager->addErrorMessage(__('Reservation item is not cancelable.'));
                $resultRedirect->setPath($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } catch (NoSuchEntityException $e) {
            // saved in var/log/reservation.log
            $this->logger->info('Error cancel reservation item. Message = ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Cancel reservation item failed, reservation item not found.'));
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        } catch (\Exception $e) {
            // saved in var/log/reservation.log
            $this->logger->info('Error cancel reservation item. Message = ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Cancel reservation item failed, a system error occurred.'));
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $data->setFlag(ReservationItemInterface::FLAG_CANCEL);

        try {
            $this->logger->info('cancel reservation item id = ' . $data->getId() . ' success');
            $this->update($data);
            $this->messageManager->addSuccessMessage(__('Cancel reservation item success.'));
        } catch (\Exception $e) {
            $this->logger->info('cancel reservation id = ' . $data->getId() . ' failed. Message = ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Cancel reservation item failed, a system error occurred.'));
        }



        $resultRedirect->setPath($this->_redirect->getRefererUrl());
        // $resultRedirect->setPath('reservation/customer/detail', ['id' => $data->getId()]);
        $this->logger->info('========= Cancel Reservation Item by Customer End ==========');
        return $resultRedirect;
    }

    /**
     * update data reservation item
     *
     * @param \Trans\Reservation\Api\Data\ReservationItemInterface $reservationItem
     * @return void
     */
    protected function update(\Trans\Reservation\Api\Data\ReservationItemInterface $reservationItem)
    {
        $this->reservationItemRepository->save($reservationItem);
        $this->reservationHelper->releaseReservationItemStock($reservationItem, $reservationItem->getSourceCode());

        $this->updateReservation($reservationItem->getReservationId());
    }

    /**
     * update data reservation
     *
     * @param int $reservationId
     * @return void
     */
    protected function updateReservation(int $reservationId)
    {
        $this->logger->info('--start run ' . __FUNCTION__);

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

            if((int)$countItems === (int)count($itemCancelled)) {
                $reservation->setFlag(ReservationInterface::FLAG_CANCEL);
                $this->reservationRepository->save($reservation);
                $this->logger->info('success update reservationId = ' . $reservation->getId());
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->info('error ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->info('error ' . $e->getMessage());
        }

        $this->logger->info('--end run ' . __FUNCTION__);
    }
}
