<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Customer
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Customer\Controller\Otp;

class Update extends \Trans\Customer\Controller\Otp\Index
{
    /**
     * execute page
     */
    public function execute()
    {
        if(!$this->customerSession->getFormPost()) {
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $formPost = $this->customerSession->getFormPost();
        $telephone = $formPost['telephone'];

        $this->registry->register('telephone', $telephone);
        $this->registry->register('formPost', $formPost);
        $this->registry->register('verification_id', $this->customerSession->getVerificationId());

        return $this->pageFactory->create();
    }
}
