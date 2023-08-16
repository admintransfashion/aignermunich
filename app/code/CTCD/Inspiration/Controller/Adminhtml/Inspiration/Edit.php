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

class Edit extends \CTCD\Inspiration\Controller\Adminhtml\Inspiration
{

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $recordId = $this->getRequest()->getParam('id');

        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('CTCD_Core::core');

        $resultPage->getConfig()->getTitle()->prepend(__('Inspiration'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Inspiration'));

        if ($recordId === null) {
            $resultPage->getConfig()->getTitle()->prepend(__('New Inspiration'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Inspiration #%1', $recordId));
        }

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CTCD_Inspiration::inspiration_update');
    }
}
