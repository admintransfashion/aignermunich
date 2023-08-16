<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Controller\Adminhtml\Inspiration;

class View extends \CTCD\Inspiration\Controller\Adminhtml\Inspiration
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
            return $resultRedirect->setPath('ctcdinspiration/*/');
        }

        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('CTCD_Core::core');

        $resultPage->getConfig()->getTitle()->prepend(__('Inspiration'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Inspiration'));
        $resultPage->getConfig()->getTitle()->prepend(__('Inspiration Detail #%1', $recordId));

        return $resultPage;
    }
}
