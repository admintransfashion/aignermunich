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

namespace CTCD\TCastSMS\Plugin\Config\Controller\Adminhtml\System\Config;

use CTCD\TCastSMS\Helper\Data as SMSHelper;

class Save
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \CTCD\TCastSMS\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \CTCD\TCastSMS\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \CTCD\TCastSMS\Helper\Data $dataHelper
    ) {
        $this->request = $request;
        $this->dataHelper = $dataHelper;
    }

    public function beforeExecute(
        \Magento\Config\Controller\Adminhtml\System\Config\Save $subject
    ) {
        $groups = $subject->getRequest()->getPostValue('groups');

        if (isset($groups['general']['fields']['api_key'])) {
            $groups['general']['fields']['api_key']['value'] = $this->dataHelper->getApiKey();
        }

        if (isset($groups['general']['fields']['client_id'])) {
            $groups['general']['fields']['client_id']['value'] = $this->dataHelper->getClientId();
        }

        if (isset($groups['general']['fields']['base_url'])) {
            $baseUrl = trim($groups['general']['fields']['base_url']['value']);
            $baseUrl = preg_replace('/^\/+|\/+$/', '', $baseUrl) . '/';
            $groups['general']['fields']['base_url']['value'] = $baseUrl ? $baseUrl : SMSHelper::DEFAULT_BASE_URL;
        }

        if (isset($groups['code_otp']['fields']['total_char'])) {
            $totalChar = (int) $groups['code_otp']['fields']['total_char']['value'];
            $totalChar = ($totalChar >= SMSHelper::DEFAULT_MIN_TOTAL_CHAR && $totalChar <= SMSHelper::DEFAULT_MAX_TOTAL_CHAR) ? $totalChar : (int) SMSHelper::DEFAULT_MIN_TOTAL_CHAR;
            $groups['code_otp']['fields']['total_char']['value'] = $totalChar;
        }

        if (isset($groups['code_otp']['fields']['otp_lifetime'])) {
            $otpLifetime = (int) $groups['code_otp']['fields']['otp_lifetime']['value'];
            $groups['code_otp']['fields']['otp_lifetime']['value'] = $otpLifetime > 0 ? $otpLifetime : SMSHelper::DEFAULT_OTP_LIFETIME;
        }

        if (isset($groups['code_otp']['fields']['static_code'])) {
            $staticOTPCode = $groups['code_otp']['fields']['static_code']['value'];
            $defaultStaticOTP = SMSHelper::DEFAULT_STATIC_OTP_6;
            switch ($totalChar) {
                case 8:
                    $defaultStaticOTP = SMSHelper::DEFAULT_STATIC_OTP_8;
                    break;
                case 7:
                    $defaultStaticOTP = SMSHelper::DEFAULT_STATIC_OTP_7;
                    break;
                default:
                    $defaultStaticOTP = SMSHelper::DEFAULT_STATIC_OTP_6;
            }
            $groups['code_otp']['fields']['static_code']['value'] = (preg_match('/^[0-9]{6,8}$/', $staticOTPCode) && strlen($staticOTPCode) === $totalChar) ? $staticOTPCode : $defaultStaticOTP;
        }
        
        if (isset($groups['sending_otp']['fields']['max_resend'])) {
            $maxResend = (int) $groups['sending_otp']['fields']['max_resend']['value'];
            $groups['sending_otp']['fields']['max_resend']['value'] = $maxResend > 0 ? $maxResend : SMSHelper::DEFAULT_MAX_RESEND;
        }

        if (isset($groups['sending_otp']['fields']['time_interval'])) {
            $timeInterval = (int) $groups['sending_otp']['fields']['time_interval']['value'];
            $groups['sending_otp']['fields']['time_interval']['value'] = $timeInterval > 0 ? $timeInterval : SMSHelper::DEFAULT_TIME_INTERVAL;
        }

        $subject->getRequest()->setPostValue('groups', $groups);

        return [$subject];
    }
    
}
