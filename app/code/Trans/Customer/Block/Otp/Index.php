<?php

/**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Customer
 * @license  Proprietary
 *
 * @author   hadi <ashadi.sejati@transdigital.co.id>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Customer\Block\Otp;

class Index extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $customerSession;

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $registry;

    /**
     * @var \CTCD\Core\Helper\File
     */
    protected $fileHelper;

	/**
	 * @var \Trans\Core\Helper\Data
	 */
	protected $dataHelper;

    /**
     * @var \CTCD\TCastSMS\Helper\Data
     */
    protected $tcastsmsHelper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Framework\Registry $registry
	 * @param \CTCD\Core\Helper\File $fileHelper
	 * @param \Trans\Core\Helper\Data $dataHelper
	 * @param \CTCD\TCastSMS\Helper\Data $tcastsmsHelper
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Registry $registry,
        \CTCD\Core\Helper\File $fileHelper,
		\Trans\Core\Helper\Data $dataHelper,
        \CTCD\TCastSMS\Helper\Data $tcastsmsHelper
	)
	{
		$this->registry = $registry;
		$this->customerSession = $customerSession;
        $this->fileHelper = $fileHelper;
		$this->dataHelper = $dataHelper;
        $this->tcastsmsHelper = $tcastsmsHelper;

		parent::__construct($context);
	}

    /**
     * Get Ajax Request Key
     *
     * @return string
     * 
     */
    public function getAjaxRequestKey()
    {
        $key = $this->fileHelper->generateUUID();
        $this->customerSession->setAjaxRequestKey($key);
        return $key;
    }

	/**
	 * get form action
	 * 
	 * @return string
	 */
	public function getFormAction()
	{
		$url = $this->customerSession->getActionPost();
		return $this->dataHelper->getBaseUrl() . $url;
	}

	/**
	 * get verification id
	 * 
	 * @return string
	 */
	public function getVerificationId()
	{
		return $this->registry->registry('verification_id');
	}

	/**
	 * get telephone
	 * 
	 * @return string
	 */
	public function getMobileNumber()
	{
		return $this->registry->registry('telephone');
	}

	/**
     * generate form fields
     * 
     * @return mixed|string
     */
    public function generateFormFields()
    {
        $post = $this->registry->registry('register');
        $field = '';
        foreach($post as $key => $row) {
            if($key == 'form_key') {
                continue;
            }
            $field .= "<input type='hidden' name='" . $key . "' value='" . $row . "'>";
        }

        return $field;
    }

    /**
     * get send sms verification url
     * 
     * @return string
     */
    public function getSendUrl()
    {
		return $this->dataHelper->getBaseUrl() . 'rest/default/V1/customer/sendOtp';
    }

    /**
     * get max resend
     * 
     * @return int
     */
    public function getMaxResend()
    {
    	return $this->tcastsmsHelper->getOTPMaxResend();
    }

    /**
     * Check whether the static OTP configuration is active
     * 
     * @return bool
     */
    public function isStaticOTPEnabled()
    {
    	return $this->tcastsmsHelper->isStaticOTPEnabled();
    }

    /**
     * Get target url
     *
     * @return string
     */
    public function getTargetUrl()
    {
    	return $this->tcastsmsHelper->getTargetUrl();
    }

    /**
     * get verify url
     * 
     * @return string
     */
    public function getVerifyUrl()
    {
		return $this->dataHelper->getBaseUrl() . 'rest/default/V1/customer/verifyOtp';
    }

    /**
     * get OTP Code Length from configuration
     * 
     * @return int
     */
    public function getOTPCodeLength()
    {
        return $this->tcastsmsHelper->getOTPCodeLength();
    }
}