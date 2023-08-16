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

class Sender extends AbstractHelper
{
    const PATH_URL_BALANCE = 'Balance';
    const PATH_URL_SEND_SMS = 'SendSMS';
    const PATH_URL_STATUS_SMS = 'MessageStatus';

    /**
     * @var \CTCD\Core\Helper\Curl
     */
    protected $curlHelper;

    /**
     * @var \CTCD\Core\Helper\Json
     */
    protected $jsonHelper;

    /**
     * @var \CTCD\TCastSMS\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \CTCD\Core\Helper\Curl $curlHelper
     * @param \CTCD\Core\Helper\Json $jsonHelper
     * @param \CTCD\TCastSMS\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \CTCD\Core\Helper\Curl $curlHelper,
        \CTCD\Core\Helper\Json $jsonHelper,
        \CTCD\TCastSMS\Helper\Data $dataHelper
    ) {
        $this->curlHelper = $curlHelper;
        $this->jsonHelper = $jsonHelper;
        $this->dataHelper = $dataHelper;

        parent::__construct($context);
    }

    /**
     * Get Data Helper object
     *
     * @return \CTCD\TCastSMS\Helper\Data
     * 
     */
    public function getConfigHelper()
    {
        return $this->dataHelper;
    }

    /**
     * Send SMS of OTP
     *
     * @param string $mobileNumber
     * @param string|null $verificationData
     * 
     * @return array
     * 
     */
    public function sendSmsOTP(string $mobileNumber, string $verificationData = null)
    {
        $responses['statusResponse'] = '{}';
        $responses['messageId'] = '';
        $responses['delivered'] = false;
        $requestSendOTPResp = $this->requestSendOTP($mobileNumber, $verificationData);
        $responses['sendResponse'] = $requestSendOTPResp;
        $sendOTPResp = $requestSendOTPResp ? $this->jsonHelper->unserializeJson($requestSendOTPResp) : [];
        if (isset($sendOTPResp['Data'][0]) && isset($sendOTPResp['Data'][0]['MessageErrorCode']) && $sendOTPResp['Data'][0]['MessageErrorCode'] === 0) {
            $messageId = $sendOTPResp['Data'][0]['MessageId'] ? $sendOTPResp['Data'][0]['MessageId'] : '';
            if ($messageId) {
                $isDelivered = false;
                $iteration = 0;
                // check the status of otp delivery to the customer 6 times, with an interval of 5 seconds for each check.
                while (!$isDelivered && $iteration < 3) {
                    sleep(5);
                    $requestMessageStatusResp = $this->requestOTPStatus($messageId);
                    $responses['statusResponse'] = $requestMessageStatusResp;
                    $messageStatusResp = $requestMessageStatusResp ? $this->jsonHelper->unserializeJson($requestMessageStatusResp) : [];
                    if ($messageStatusResp) {
                        if (isset($messageStatusResp['Data']['MessageId'])) {
                            $responses['messageId'] = $messageStatusResp['Data']['MessageId'];
                        }
                        if (isset($messageStatusResp['Data']['ErrorCode']) && $messageStatusResp['Data']['ErrorCode'] === '0') {
                            $responses['delivered'] = true;
                            $isDelivered = true;
                        }
                    }
                    $iteration++;
                }
            }
        }
        return $responses;
    }

    /**
     * Send SMS OTP request
     *
     * @param string $telephone
     * @param string|null $verificationData
     * 
     * @return string|null
     * 
     */
    public function requestSendOTP(string $mobileNumber, string $verificationData = null)
    {
        $requestUrl = '';
        $verification = $verificationData ? $this->jsonHelper->unserializeJson($verificationData) : [];
        if ($verification && $verification['code'] && preg_match('/^08[1-9][0-9]{7,11}$/', $mobileNumber)) {
            $mobileNumber = preg_replace('/^0{1}/', '62', $mobileNumber);
            $requestUrl = $this->prepareSendOTPUrl($mobileNumber, $verification['code']);
        }
        return $requestUrl ? $this->curlHelper->curlGet($requestUrl) : null;
    }

    /**
     * Get Status of SMS OTP request
     *
     * @return string|null
     * 
     */
    public function requestOTPStatus($messageId)
    {
        $requestUrl = $this->prepareStatusOTPUrl($messageId);
        return $requestUrl ? $this->curlHelper->curlGet($requestUrl) : null;
    }

    /**
     * Get Balance API request
     *
     * @return string|null
     * 
     */
    public function requestBalance()
    {
        $requestUrl = $this->prepareBalanceUrl();
        return $requestUrl ? $this->curlHelper->curlGet($requestUrl) : null;
    }

    /**
     * @param mixed $balanceResponse
     * 
     * @return float
     */
    public function getBalanceNominal($balanceResponse)
    {
        $balance = 0;
        if ($balanceResponse) {
            $arrData = $this->jsonHelper->unserializeJson($balanceResponse);
            if ($arrData && (int) $arrData['ErrorCode'] === 0) {
                $credit = $arrData['Data'][0]['Credits'] ? $arrData['Data'][0]['Credits'] : null;
                $balance = $credit ? number_format(floatval(str_replace('IDR', '', $credit)), 2) : 0;
            }
        }
        return $balance;
    }

    /**
     * Prepare URL for balance request
     *
     * @param string $mobileNumber
     * @param string $otpCode
     * 
     * @return string
     * 
     */
    protected function prepareSendOTPUrl(string $mobileNumber, string $otpCode)
    {
        $apiKey = $this->dataHelper->getApiKey();
        $clientId= $this->dataHelper->getClientId();
        $senderId = $this->dataHelper->getSenderId();
        $message = $this->dataHelper->getFormattedMessage($otpCode);
        $query = [
            'ApiKey' => $apiKey,
            'ClientId' => $clientId,
            'SenderId' => $senderId,
            'Message' => $message,
            'MobileNumbers' => $mobileNumber,
            'Is_Unicode' => 'false',
            'Is_Flash' => 'false'
        ];
        $response = $apiKey && $clientId && $senderId && $message && $mobileNumber ? $this->dataHelper->getBaseUrl() . self::PATH_URL_SEND_SMS . '?' . http_build_query($query) : '';
        return $response;
    }

    /**
     * Prepare URL for balance request
     *
     * @return string
     * 
     */
    protected function prepareStatusOTPUrl($messageId)
    {
        $apiKey = $this->dataHelper->getApiKey();
        $clientId= $this->dataHelper->getClientId();
        $query = [
            'ApiKey' => $apiKey,
            'ClientId' => $clientId,
            'MessageId' => $messageId
        ];
        $response = $apiKey && $clientId && $messageId ? $this->dataHelper->getBaseUrl() . self::PATH_URL_STATUS_SMS . '?' . http_build_query($query) : '';
        return $response;
    }

    /**
     * Prepare URL for balance request
     *
     * @return string
     * 
     */
    protected function prepareBalanceUrl()
    {
        $apiKey = $this->dataHelper->getApiKey();
        $clientId= $this->dataHelper->getClientId();
        $query = [
            'ApiKey' => $apiKey,
            'ClientId' => $clientId
        ];
        $response = $apiKey && $clientId ? $this->dataHelper->getBaseUrl() . self::PATH_URL_BALANCE . '?' . http_build_query($query) : '';
        return $response;
    }
}
