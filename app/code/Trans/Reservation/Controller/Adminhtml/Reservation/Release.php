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
 * Class Release
 */
class Release extends Action
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Trans\Reservation\Model\ResouceModel\Reservation\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationHelper;

    /**
     * View constructor.
     * @param Context $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Trans\Reservation\Model\ResouceModel\Reservation\CollectionFactory $collectionFactory
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Helper\Reservation $reservationHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory $collectionFactory,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Helper\Reservation $reservationHelper
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
        $this->reservationRepository = $reservationRepository;
        $this->reservationHelper = $reservationHelper;

        parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $reservationId = $this->getRequest()->getParam('id');
        $reservation = $this->reservationRepository->getById($reservationId);

        if($reservation->getFlag() == ReservationInterface::FLAG_FINISH) {
            $this->messageManager->addErrorMessage('Reservation attendance already confirmed.');
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $this->reservationHelper->releaseReservationItems($reservation);

        if($reservationId){
            $reservation->setDateConfirm($this->timezone->date());
            $reservation->setFlag(ReservationInterface::FLAG_FINISH);
            $this->reservationRepository->save($reservation);

            $this->reservationHelper->updateReservationItemsByReservationId($reservationId, ReservationItemInterface::FLAG_CONFIRM);
        }

        $this->messageManager->addSuccessMessage('Confirmation attendance success.');
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

}
