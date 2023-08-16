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

namespace Trans\Reservation\Controller\Adminhtml\Reservation;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;

/**
 * Class ReleaseItem
 */
class ReleaseItem extends Action
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Trans\Reservation\Model\ResouceModel\ReservationItem\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationHelper;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * View constructor.
     * @param Context $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Trans\Reservation\Model\ResouceModel\ReservationItem\CollectionFactory $collectionFactory
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $itemRepository
     * @param \Trans\Reservation\Helper\ReservationItem $reservationHelper
     * @param \Trans\Reservation\Helper\Data $dataHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory $collectionFactory,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $itemRepository,
        \Trans\Reservation\Helper\Reservation $reservationHelper,
        \Trans\Reservation\Helper\Data $dataHelper
    )
    {
        $this->dataHelper = $dataHelper;
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
        $this->itemRepository = $itemRepository;
        $this->reservationHelper = $reservationHelper;

        $this->logger = $dataHelper->getLogger();

        parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $this->logger->info('----start ' . __CLASS__);
        $resultRedirect = $this->resultRedirectFactory->create();

        $err = 0;
        $orderId = $this->getRequest()->getParam('id');
        $items = $this->itemRepository->getByReference($orderId);

        foreach($items as $item) {

            if($item->getFlag() == ReservationItemInterface::FLAG_CONFIRM) {
                $this->messageManager->addErrorMessage('Reservation attendance already confirmed.');
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }

            try {
                $this->reservationHelper->releaseReservationItemStock($item);
                $this->reservationHelper->updateReservationItem($item->getId(), ReservationItemInterface::FLAG_CONFIRM);

                /* dispatch */
                $this->_eventManager->dispatch('delivered_reservation_order', ['order_id' => $orderId]);
            } catch (\Exception $e) {
                $this->logger->info('error : ' . $e->getMessage());
                $err++;
            }
        }

        if($err) {
            $this->messageManager->addErrorMessage('Confirmation attendance failed.');
        } else {
            $this->messageManager->addSuccessMessage('Confirmation attendance success.');
        }

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        $this->logger->info('----end ' . __CLASS__);
        return $resultRedirect;
    }

}
