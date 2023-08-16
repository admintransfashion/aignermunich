<?php
/**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_TCastSMS
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\TCastSMS\Controller\Adminhtml\Log;

class View extends \CTCD\TCastSMS\Controller\Adminhtml\Log
{

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {

        $recordId = $this->getRequest()->getParam('id');
        if(! $recordId){
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__("Parameter 'id' is required."));
            return $resultRedirect->setPath('tcastsms/*/');
        }

        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('CTCD_Core::core');

        $resultPage->getConfig()->getTitle()->prepend(__('TCastSMS'));
        $resultPage->getConfig()->getTitle()->prepend(__('OTP Log'));
        $resultPage->getConfig()->getTitle()->prepend(__('Log Detail #%1', $recordId));

        return $resultPage;
    }
}
