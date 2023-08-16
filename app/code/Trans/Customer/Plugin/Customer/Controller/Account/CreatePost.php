<?php 

/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy Surya Perdana <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Plugin\Customer\Controller\Account;

use Magento\Customer\Controller\Account\CreatePost as MageCreatePost;
use Magento\Framework\UrlInterface;

/**
 *  Class Create Post
 */
class CreatePost 
{
	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request;

	/**
	 * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
	 */
	protected $customerFactory;

	/**
	 * @param \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $redirect;

	/**
	 * @param \Magento\Framework\UrlInterface
	 */
	protected $urlInterface;

	/**
	 * @param \Magento\Framework\Controller\ResultFactory
	 */
	protected $resultFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

	/**
	 * @param \Magento\Customer\Model\Customer
	 */
	protected $customerModel;

	/**
	 * @param \Magento\Customer\Model\Session
	 */
	protected $customerSession;

	/**
	 * @param \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;

    /**
     * @var \CTCD\Core\Helper\Json
     */
    protected $jsonHelper;

    /**
     * @var \Trans\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

	/**
	 * @param \CTCD\TCastSMS\Helper\Data
	 */
	protected $tcastConfigHelper;

	/**
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
	 * @param \Magento\Framework\App\Response\RedirectInterface $redirect
	 * @param \Magento\Framework\UrlInterface $urlInterface
	 * @param \Magento\Framework\Controller\ResultFactory $resultFactory
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Customer\Model\Customer $customerModel
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \CTCD\Core\Helper\Json $jsonHelper
	 * @param \Trans\Customer\Api\AccountManagementInterface $accountManagement
	 * @param \CTCD\TCastSMS\Helper\Data $tcastConfigHelper
	 */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Message\ManagerInterface $messageManager,
        \CTCD\Core\Helper\Json $jsonHelper,
        \Trans\Customer\Api\AccountManagementInterface $accountManagement,
		\CTCD\TCastSMS\Helper\Data $tcastConfigHelper
	)
	{	
		$this->redirect = $redirect;
		$this->urlInterface = $urlInterface;
		$this->resultFactory = $resultFactory;
        $this->registry = $registry;
        $this->customerModel = $customerModel;
        $this->customerSession = $customerSession;
		$this->messageManager = $messageManager;
		$this->request = $request;
		$this->customerFactory = $customerFactory;
        $this->jsonHelper = $jsonHelper;
        $this->accountManagement = $accountManagement;
		$this->tcastConfigHelper = $tcastConfigHelper;
	}
	
	/**
	 * phone number validation process
	 * 
	 * @param MageCreatePost $subject
	 * @param callable $proceed
	 * @return mixed
	 */
	public function aroundExecute(MageCreatePost $subject, callable $proceed)
	{ 
		$phoneNumber = $this->request->getParam('telephone');
		$email = $this->request->getParam('email');

		if(!preg_match('/^08[1-9][0-9]{7,11}$/', $phoneNumber)) {
			$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
			$this->messageManager->addError(__('Phone number is not valid'));
            $params = ['register' => '1'];
            $resultRedirect->setPath('*/*/login', $params);
            $this->customerSession->setCustomerFormData($this->request->getPostValue());
			return $resultRedirect;
		}

		$customerData = $this->customerModel->getCollection()->addFieldToFilter('telephone', $phoneNumber);

		 //check data telephone is already exist or not
        if($customerData->getSize() > 0) { 
        	foreach($customerData as $customerdatas){ 
                $customerdatas['telephone'];
			}
        	//get message error when data customer telephone already exist
            $this->messageManager->addError(__('Your email/mobile number has already been registered.'));
            $params = ['register' => '1'];
        	$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/login', $params);
            $this->customerSession->setCustomerFormData($this->request->getPostValue());
			return $resultRedirect;
		}

		$customerDataEmail = $this->customerModel->getCollection()->addFieldToFilter('email', $email);

		if($customerDataEmail->getSize() > 0) { 
        	foreach($customerDataEmail as $customerDataEmails){ 
                $customerDataEmails['email'];
			}
        	//get message error when data customer telephone already exist
            $this->messageManager->addError(__('Your email/mobile number has already been registered.'));
            $params = ['register' => '1'];
        	$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/login', $params);
            $this->customerSession->setCustomerFormData($this->request->getPostValue());
			return $resultRedirect;
		}
	
		if($this->tcastConfigHelper->isModuleEnabled() && !$this->request->getParam('otp')) {
			$this->customerSession->setRegister($this->request->getParams());
			$this->customerSession->setActionPost('customer/account/createpost');
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            if ($this->sendOTP($phoneNumber)) {
                $resultRedirect->setPath($this->urlInterface->getUrl('customer/otp/index'));
            } else {
                $this->messageManager->addError(__('An error has occured when sending the OTP to you. Sorry for the inconvenience. please try again later.'));
                $params = ['register' => '1'];
                $resultRedirect->setPath('*/*/login', $params);
                $this->customerSession->setCustomerFormData($this->request->getPostValue());
            }
            
			return $resultRedirect;
		}

		if($this->tcastConfigHelper->isModuleEnabled() && $this->customerSession->getVerified() != true) {
			$resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
			$this->messageManager->addError(__('Make sure the code is correct.'));
			$resultRedirect->setPath($this->redirect->getRefererUrl());
			return $resultRedirect;
		}
		
		$this->customerSession->unsRegister();
		$this->customerSession->unsActionPost();
        $this->customerSession->unsVerificationId();
		$this->customerSession->unsVerified();
		
		return $proceed();
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
