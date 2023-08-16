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

class Index extends \CTCD\TCastSMS\Controller\Adminhtml\Log
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        /**
         * Set active menu item
         */
        $resultPage->setActiveMenu('CTCD_Core::core');
        $resultPage->getConfig()->getTitle()->prepend(__('TCastSMS'));
        $resultPage->getConfig()->getTitle()->prepend(__('OTP Log'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('TCastSMS'),__('OTP Log'));

        return $resultPage;
    }
}
