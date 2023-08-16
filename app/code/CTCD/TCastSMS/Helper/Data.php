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

namespace CTCD\TCastSMS\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const DEFAULT_MIN_TOTAL_CHAR = 6;
    const DEFAULT_MAX_TOTAL_CHAR = 8;
    const DEFAULT_OTP_LIFETIME = 5;
    const DEFAULT_MAX_RESEND = 5;
    const DEFAULT_TIME_INTERVAL = 60;
    const DEFAULT_STATIC_OTP_6 = 123456;
    const DEFAULT_STATIC_OTP_7 = 1234567;
    const DEFAULT_STATIC_OTP_8 = 12345678;
    const DEFAULT_BASE_URL = 'https://api.tcastsms.net/api/v2/';
    const DEFAULT_SENDER_ID = 'ACM';
    const DEFAULT_OTP_MESSAGE = 'Your AIGNER OTP code to login is %OTP_CODE%. This code will remain valid for %OTP_LIFETIME% minutes. Please do not share it with anyone.';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param string $field
     * @param string|null $scope
     * @param int|null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $scope = null, $storeId = null)
    {
        $scope = !$scope ? ScopeInterface::SCOPE_WEBSITE : $scope;
        return $this->scopeConfig->getValue(
            $field,
            $scope,
            $storeId
        );
    }

    /**
     * Is module enabled?
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return (bool) $this->getConfigValue('tcastsms_otp/general/active');
    }

    /**
     * Get API Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return trim($this->getConfigValue('tcastsms_otp/general/api_key'));
    }

    /**
     * Get Client ID
     *
     * @return string
     */
    public function getClientId()
    {
        return trim($this->getConfigValue('tcastsms_otp/general/client_id'));
    }

    /**
     * Get Sender ID
     *
     * @return string
     */
    public function getSenderId()
    {
        $value = $this->getConfigValue('tcastsms_otp/general/sender_id');
        return $value ? $value : self::DEFAULT_SENDER_ID;
    }

    /**
     * Get base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $value = trim($this->getConfigValue('tcastsms_otp/general/base_url'));
        $value = preg_replace('/^\/+|\/+$/', '', $value) . '/';
        return $value ? $value : self::DEFAULT_BASE_URL;
    }

    /**
     * Get total character/digit of OTP code
     *
     * @return integer
     */
    public function getOTPCodeLength()
    {
        $value = abs((int) $this->getConfigValue('tcastsms_otp/code_otp/total_char'));
        return ($value >= self::DEFAULT_MIN_TOTAL_CHAR && $value <= self::DEFAULT_MAX_TOTAL_CHAR) ? $value : self::DEFAULT_MIN_TOTAL_CHAR;
    }

    /**
     * Get OTP active lifetime
     *
     * @return integer
     */
    public function getOTPLifetime()
    {
        $value = abs((int) $this->getConfigValue('tcastsms_otp/code_otp/otp_lifetime'));
        return $value > 0 ? $value : self::DEFAULT_OTP_LIFETIME;
    }

    /**
     * Is static OTP enabled?
     *
     * @return bool
     */
    public function isStaticOTPEnabled()
    {
        return (bool) $this->getConfigValue('tcastsms_otp/code_otp/active_static');
    }

    /**
     * Get static OTP Code
     *
     * @return string
     */
    public function getStaticOTP()
    {
        $totalChar = $this->getOTPCodeLength();
        $defaultStaticOTP = self::DEFAULT_STATIC_OTP_6;
        switch ($totalChar) {
            case 8:
                $defaultStaticOTP = self::DEFAULT_STATIC_OTP_8;
                break;
            case 7:
                $defaultStaticOTP = self::DEFAULT_STATIC_OTP_7;
                break;
            default:
                $defaultStaticOTP = self::DEFAULT_STATIC_OTP_6;
        }
        $value = $this->getConfigValue('tcastsms_otp/code_otp/static_code');
        $codeLength = strlen($value);
        return ($value && $codeLength >= self::DEFAULT_MIN_TOTAL_CHAR && $codeLength <= self::DEFAULT_MAX_TOTAL_CHAR) ? $value : $defaultStaticOTP;
    }

    /**
     * Get target url
     *
     * @return string
     */
    public function getTargetUrl()
    {
        $baseUrl = trim(trim($this->storeManager->getStore()->getBaseUrl()), '/');
        $value = $this->getConfigValue('tcastsms_otp/code_otp/target_url');
        return $value ? $baseUrl . '/' . $value : $baseUrl;
    }

    /**
     * Is static OTP enabled?
     *
     * @return bool
     */
    public function isTimeIntervalEnabled()
    {
        return (bool) $this->getConfigValue('tcastsms_otp/sending_otp/active_interval');
    }

    /**
     * Get OTP time interval per each send
     *
     * @return integer
     */
    public function getOTPTimeInterval()
    {
        $value = abs((int) $this->getConfigValue('tcastsms_otp/sending_otp/time_interval'));
        return $value > 0 ? $value : self::DEFAULT_TIME_INTERVAL;
    }

    /**
     * Get OTP maximum resend
     *
     * @return integer
     */
    public function getOTPMaxResend()
    {
        $value = abs((int) $this->getConfigValue('tcastsms_otp/sending_otp/max_resend'));
        return $value > 0 ? $value : self::DEFAULT_MAX_RESEND;
    }

    /**
     * Get SMS Message Template
     *
     * @return string
     */
    public function getMessageTemplate()
    {
        $value = $this->getConfigValue('tcastsms_otp/message_otp/message_template');
        return $value ? $value : self::DEFAULT_OTP_MESSAGE;
    }

    /**
     * Get Formatted Message
     *
     * @return string
     */
    public function getFormattedMessage($otpCode)
    {
        $message = $this->getMessageTemplate();
        $message = str_replace('%OTP_CODE%', $otpCode, $message);
        $message = str_replace('%OTP_LIFETIME%', $this->getOTPLifetime(), $message);
        return $message ? $message : self::DEFAULT_OTP_MESSAGE;
    }
    
    /**
     * Get OTP Code
     *
     * @return string
     */
    public function getOTPCode()
    {
        $totalChar = $this->getOTPCodeLength();
        $otpCode = $this->isStaticOTPEnabled() ? $this->getStaticOTP() : $this->generateOTP($totalChar);
        return $this->isModuleEnabled() ? $otpCode : '';
    }

    /**
     * Generate OTP Code
     * 
     * @return string
     */
    public function generateOTP($totalChar)
    {
        $chars = '0123456789';
        $otpCode = '';
        for ($i = 0; $i < $totalChar; $i++) {
            $otpCode .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $otpCode;
    }
}

