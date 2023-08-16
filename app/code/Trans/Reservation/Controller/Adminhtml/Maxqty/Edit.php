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

namespace Trans\Reservation\Controller\Adminhtml\Maxqty;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 */
class Edit extends \Magento\Backend\App\Action
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
     * @var \Trans\Reservation\Controller\Adminhtml\Downloader
     */
    protected $downloader;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Trans\Reservation\Controller\Adminhtml\Downloader $downloader
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Trans\Reservation\Controller\Adminhtml\Downloader $downloader
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->downloader = $downloader;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $param = $this->getRequest()->getParams();

        if(isset($param['download'])) {
            try {
                return $this->downloader->download($param['download']);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addError(__('There is no sample file for this entity.'));

                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('reservation/config/import');

                return $resultRedirect;
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Trans_Reservation::reservation_maxqty')
                ->getConfig()->getTitle()->prepend(__('Create Max Qty Product Config'));
        return $resultPage;
    }

}
