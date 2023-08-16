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

class Index extends \CTCD\Inspiration\Controller\Adminhtml\Inspiration
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
        $resultPage->getConfig()->getTitle()->prepend(__('Inspiration'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Inspiration'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Inspiration'),__('Manage Inspiration'));

        return $resultPage;
    }
}
