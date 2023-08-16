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

namespace Trans\Customer\Controller\Account;

use Magento\Framework\App\Request\InvalidRequestException;

class ChangeTelephonePost extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customerModel;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var \CTCD\Core\Helper\Json
     */
    protected $jsonHelper;

    /**
     * @var \Trans\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \CTCD\TCastSMS\Helper\Data
     */
    protected $configHtcastConfigHelperelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \CTCD\Core\Helper\Json $jsonHelper
     * @param \Trans\Customer\Api\AccountManagementInterface $accountManagement
     * @param \CTCD\TCastSMS\Helper\Data $tcastConfigHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \CTCD\Core\Helper\Json $jsonHelper,
        \Trans\Customer\Api\AccountManagementInterface $accountManagement,
        \CTCD\TCastSMS\Helper\Data $tcastConfigHelper
    ) {
        $this->request = $request;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerSession = $customerSession;
        $this->customerModel = $customerModel;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->jsonHelper = $jsonHelper;
        $this->accountManagement = $accountManagement;
        $this->tcastConfigHelper = $tcastConfigHelper;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        \Magento\Framework\App\RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(\Magento\Framework\App\RequestInterface $request): ?bool
    {
        return null;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if(! $this->customerSession->isLoggedIn()){
            $this->messageManager->addErrorMessage(__('You have to login to change your phone number'));
            return $resultRedirect->setPath('customer/account/login');
        }

        $validFormKey = $this->formKeyValidator->validate($this->request);
        if ($validFormKey && $this->request->isPost()) {
            $customer = $this->customerSession->getCustomer();
            if($customer && $customer->getId()){
                $oldTelephone = $customer->getTelephone();
                $newTelephone = $this->request->getParam('telephone') ?: false;

                if(!preg_match('/^08[1-9][0-9]{7,11}$/', $newTelephone)) {
                    $this->messageManager->addErrorMessage(__('Phone number is not valid'));
                    return $resultRedirect->setPath('*/*/edit');
                }
                else{
                    $customerData = $this->customerModel->getCollection()->addFieldToFilter('telephone', $newTelephone);
                    if($customerData->getSize() > 0)
                    {
                        $this->messageManager->addErrorMessage(__('Your phone number has already been registered'));
                        return $resultRedirect->setPath('*/*/edit');
                    }
                    else{
                        if($this->tcastConfigHelper->isModuleEnabled() && !$this->request->getParam('otp')) {
                            $this->customerSession->setFormPost($this->request->getParams());
                            $this->customerSession->setActionPost('customer/account/changetelephonepost');
                            if ($this->sendOTP($newTelephone)) {
                                return $resultRedirect->setPath('customer/otp/update');
                            } else {
                                $this->messageManager->addError(__('An error has occured when sending the OTP to you. Sorry for the inconvenience. please try again later.'));
                                return $resultRedirect->setPath('*/*/edit');
                            }
                        }
                        if($this->tcastConfigHelper->isModuleEnabled() && $this->customerSession->getVerified() != true) {
                            $this->messageManager->addError(__('Make sure the code is correct.'));
                            return $resultRedirect->setPath($this->redirect->getRefererUrl());
                        }

                        $customerRepo = $this->customerRepositoryInterface->getById($customer->getId());
                        $customerRepo->setCustomAttribute('telephone', $newTelephone);
                        $this->customerRepositoryInterface->save($customerRepo);

                        $this->customerSession->unsFormPost();
                        $this->customerSession->unsActionPost();
                        $this->customerSession->unsVerified();

                        $this->messageManager->addSuccessMessage(__('Your phone number has been updated successfully.'));
                        return $resultRedirect->setPath('customer/account');
                    }
                }
            }
            else{
                $this->customerSession->logout();
                $this->customerSession->start();
                $this->messageManager->addErrorMessage(__('You have to login to change your phone number'));
                return $resultRedirect->setPath('customer/account/login');
            }
        }

        return $resultRedirect->setPath('*/*/edit');
    }

    /**
     * Send an OTP process
     *
     * @param string $phoneNumber
     * 
     * @return boolean
     * 
     */
    protected function sendOTP($phoneNumber)
    {
        $success = false;
        if($phoneNumber) {
            $response = $this->accountManagement->sendSmsOtp($phoneNumber);
            $arrResponse = $this->jsonHelper->unserializeJson($response);
            if($arrResponse) {
                if(isset($arrResponse['verification_id']) && $arrResponse['verification_id']) {
                    $this->customerSession->setVerificationId($arrResponse['verification_id']);
                }
                if((isset($arrResponse['delivered']) && $arrResponse['delivered']) || $this->tcastConfigHelper->isStaticOTPEnabled()) {
                    $success = true;
                }
            }
            
        }
        return $success;
    }
}
