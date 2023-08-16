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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class View
 */
class View extends \Magento\Backend\App\Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Trans_Reservation::reservation';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
     */
    protected $reservationItemRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
    )
    {
        $this->coreRegistry = $coreRegistry;
        $this->reservationRepository = $reservationRepository;
        $this->reservationItemRepository = $reservationItemRepository;
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $reservationId = $this->getRequest()->getParam('id');
        $refNumber = $this->getRequest()->getParam('ref_number');

        $reservation = $this->reservationRepository->getById($reservationId);
        $reservationItem = $this->reservationItemRepository->getByReference($refNumber);

        // Save data into the registry
        $this->coreRegistry->register('reservation', $reservation);
        $this->coreRegistry->register('reservationItem', $reservationItem);

        $resultPage->setActiveMenu('Trans_Reservation::reservation')
                ->getConfig()->getTitle()->prepend(__('View Reservation'));
        return $resultPage;
    }

}
