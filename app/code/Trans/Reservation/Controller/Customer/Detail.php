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

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Detail
 */
class Detail extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->reservationRepository = $reservationRepository;

        parent::__construct($context);
    }

    /**
     * display detail reservation page
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }

        $reservationId = $this->getRequest()->getParam('id');

        if(!$reservationId) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $reservation = $this->reservationRepository->getById($reservationId);

        // Save data into the registry
        $this->coreRegistry->register('reservation', $reservation);

        $pageFactory = $this->resultPageFactory->create();

        // Add title
        $pageFactory->getConfig()->getTitle()->set(__('Detail Reservation'));

        return $pageFactory;
    }
}
