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
namespace Trans\Reservation\Controller\Cart;

use Trans\Reservation\Api\Data\ReservationInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * Class Delete
 */
class Delete extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var logger
     */
    protected $logger;

    /**
	 * @var \Trans\Reservation\Api\ReservationRepositoryInterface
	 */
	protected $reservationRepository;

	/**
	 * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
	 */
	protected $reservationItemRepository;

	/**
	 * @var \Trans\Reservation\Helper\Data
	 */
	protected $dataHelper;

	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger,
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
     * @param \Trans\Reservation\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository,
        \Trans\Reservation\Helper\Data $dataHelper
    ) {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->reservationRepository = $reservationRepository;
        $this->reservationItemRepository = $reservationItemRepository;
        $this->dataHelper = $dataHelper;
        $this->logger = $dataHelper->getLogger();
    }

    /**
     * Add reservation action
     *
     * @return \Trans\Reservation\Api\Data\ReservationInterface
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function execute()
    {
        $this->logger->info('------------------ run delete reservation item.');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

    	// if(!$this->dataHelper->isCustomerLoggedIn()) {
     //        $this->messageManager->addErrorMessage(__('Customer harus login terlebih dahulu.'));
     //        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    	// }

        $itemId = $this->getRequest()->getParam('id');

        if(!$itemId) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        try {

        	$item = $this->reservationItemRepository->getById($itemId);
            $reservationId = $item->getReservationId();
            $reservation = $this->reservationRepository->getById($reservationId);
            $this->reservationItemRepository->delete($item);
            $items = $reservation->getItems();

            if(count($items) == 0) {
                $this->reservationRepository->delete($reservation);
                $this->customerSession->unsReservationId();
            }

        } catch (\Exception $e) {
            // saved in var/log/debug.log
            $this->logger->info('error = ' . $e->getMessage());
            $this->logger->info('$itemId ' . $itemId);

            $this->messageManager->addErrorMessage(__('Delete reservation item failed, a system error occurred.'));
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $this->messageManager->addSuccessMessage(__('Item deleted successfully.'));
        $resultRedirect->setPath($this->_redirect->getRefererUrl());
        $this->logger->info('------------------ end run delete reservation item.');
        return $resultRedirect;
    }
}
