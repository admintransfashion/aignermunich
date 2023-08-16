<?php
/**
 * @category Trans
 * @package  Trans_Reservation
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT CORP Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Reservation\Controller\Adminhtml\Reservation;
 
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;
 
/**
 * Class MassActionItem
 */
class MassActionItem extends Action
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
     * change status reservation item business status
     * 
     * @return void
     */
    public function execute()
    {
        $this->logger->info('----start ' . __CLASS__);
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->getRequest()->getParams();

        $refNumber = $params['ref'];
        $itemIds = isset($params['item']) ? $params['item'] : [];

        if(!$itemIds) {
            $this->messageManager->addErrorMessage(__('Change item(s) status failed. Please select product item(s).'));
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            $this->logger->info('----end ' . __CLASS__);
            return $resultRedirect;
        }

        $action = $params['mass_action'];

        try {
            $items = $this->itemRepository->getByReferenceItemIds($refNumber, $itemIds);

            foreach($items as $item) {
                $item->setBusinessStatus($action);

                $this->itemRepository->save($item);
            }

            $this->messageManager->addSuccessMessage(__('Change item(s) status success.'));
        } catch (\Exception $e) {
            $this->logger->info('error : ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Change item(s) status failed.'));
        }

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        $this->logger->info('----end ' . __CLASS__);
        return $resultRedirect;
    }
 
}