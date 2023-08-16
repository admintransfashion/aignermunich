<?php
 /**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Customer
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Customer\Model;

use Trans\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\AccountManagement as MageAccountManagement;
use CTCD\TCastSMS\Api\Data\LogOtpInterface;

/**
 * @api
 */
class AccountManagement implements AccountManagementInterface
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \CTCD\TCastSMS\Api\Data\LogOtpInterfaceFactory
     */
    protected $logOtp;

    /**
     * @var \CTCD\TCastSMS\Api\LogOtpRepositoryInterface
     */
    protected $logOtpRepository;

    /**
     * @var \CTCD\Core\Helper\Json
     */
    protected $jsonHelper;

    /**
     * @var \CTCD\Core\Helper\File
     */
    protected $fileHelper;

    /**
	 * @param \CTCD\TCastSMS\Helper\Sender
	 */
	protected $tcastOTPSenderHelper;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \CTCD\TCastSMS\Api\Data\LogOtpInterfaceFactory $logOtp
     * @param \CTCD\TCastSMS\Api\LogOtpRepositoryInterface $logOtpRepository
     * @param \CTCD\Core\Helper\Json $jsonHelper
     * @param \CTCD\Core\Helper\File $fileHelper
     * @param \CTCD\TCastSMS\Helper\Sender $tcastOTPSenderHelper
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \CTCD\TCastSMS\Api\Data\LogOtpInterfaceFactory $logOtp,
        \CTCD\TCastSMS\Api\LogOtpRepositoryInterface $logOtpRepository,
        \CTCD\Core\Helper\Json $jsonHelper,
        \CTCD\Core\Helper\File $fileHelper,
        \CTCD\TCastSMS\Helper\Sender $tcastOTPSenderHelper
    )
    {
        $this->dateTime = $dateTime;
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->logOtp = $logOtp;
        $this->logOtpRepository = $logOtpRepository;
        $this->jsonHelper = $jsonHelper;
        $this->fileHelper = $fileHelper;
        $this->tcastOTPSenderHelper = $tcastOTPSenderHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function sendSmsOtp(string $telephone, string $verificationId = null, $isAjax = 0, $key = null)
    {
        $reachLimit = false;

        if($isAjax) {
            if (!$key || ($key !== $this->customerSession->getAjaxRequestKey())) {
                $data['status'] = false;
                $data['message'] = __('Invalid request key. Please reload the page and try again.');
                return $this->jsonHelper->serializeJson($data);
            }
        }

        if($this->tcastOTPSenderHelper->getConfigHelper()->isStaticOTPEnabled()) {
            $staticCode = $this->tcastOTPSenderHelper->getConfigHelper()->getStaticOTP();
            $data['code'] = $staticCode;
            $data['verification_id'] = $this->fileHelper->generateUUID();
            return $this->jsonHelper->serializeJson($data);
        }

        $times = $this->logOtpRepository->countTelephoneByToday($telephone);
        $max = $this->tcastOTPSenderHelper->getConfigHelper()->getOTPMaxResend();

        if($times >= $max) {
            $reachLimit = true;
            if($isAjax) {
                if($reachLimit) {
                    $data['status'] = false;
                    $data['message'] = __('You have reached the OTP send limit of ' . $max . ' messages. Please try again later.');
                    return $this->jsonHelper->serializeJson($data);
                }
            }

            throw new StateException(
                __('You have reached the OTP send limit of ' . $max . ' messages. Please try again later.')
            );
        }
        
        $response = $this->getOtpAuthCode($telephone, $verificationId);
        $otpData = $this->jsonHelper->unserializeJson($response);
        if($response) {
            if(isset($otpData['is_diff']) && $otpData['is_diff']) {
                $sendSmsOTP = $this->tcastOTPSenderHelper->sendSmsOTP($telephone, $response);
                $this->saveResponses($otpData['verification_id'], $sendSmsOTP);
                $otpData['delivered'] = $sendSmsOTP['delivered'];
                if ($sendSmsOTP['delivered']) {
                    $this->customerSession->setVerificationId($otpData['verification_id']);
                    $this->registry->register('verification_id', $otpData['verification_id']);
                } else {
                    $otpData['message'] = 'An error has occured when sending the OTP to you. Sorry for the inconvenience. please try again later.';
                }
            }
        }

        if($verificationId) {
            if(isset($otpData['code'])) {
                unset($otpData['code']);
            }

            if(isset($otpData['is_diff']) && !$otpData['is_diff']) {
                $interval = $this->tcastOTPSenderHelper->getConfigHelper()->getOTPTimeInterval();
                $otpData['message'] = 'Please wait ' . $interval . ' second to resend the OTP';
            }

            $response = $this->jsonHelper->serializeJson($otpData);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function verifySmsOtp(string $code, string $verificationId, $websiteId = null, $key = null)
    {
        $response['status'] = false;
        if ($key && $key === $this->customerSession->getAjaxRequestKey()) {
            if($this->tcastOTPSenderHelper->getConfigHelper()->isStaticOTPEnabled()) {
                $staticCode = $this->tcastOTPSenderHelper->getConfigHelper()->getStaticOTP();
                if($code == $staticCode) {
                    $response['status'] = true;
                }
                else {
                    $response['status'] = false;
                    $response['message'] = __('Make sure the code is correct.');
                }
            } else {
                try {
                    $logOtp = $this->logOtpRepository->get($verificationId);
                    if($logOtp->getOtpCode() === $code && $this->isOtpStillValid($logOtp)) {
                        $response['status'] = true;
                        $this->setOtpVerified($verificationId);
                    }
                    else {
                        $response['status'] = false;
                        if($this->isOtpStillValid($logOtp)) {
                            $response['message'] = __('Make sure the code is correct.');
                        }
                        else {
                            $response['message'] = __('Your OTP is already expired.');
                        }
                    }
                } catch (NoSuchEntityException $e) {
                    $response['status'] = false;
                    $response['message'] = __('Make sure the code is correct.');
                }
    
                if(!$code) {
                    $response['message'] = __('Your verification code is empty.');
                }
            }
        } else {
            $response['message'] = __('Invalid request key. Please reload the page and try again.');
        }

        $this->customerSession->setVerified($response['status']);
        return $this->jsonHelper->serializeJson($response);
    }

    /**
     * {@inheritdoc}
     */
    public function getOtpAuthCode(string $telephone, string $verificationId = null)
    {
        $isValid = true;
        if($verificationId) {
            try {
                $logOtp = $this->logOtpRepository->get($verificationId);
                $isValid = $this->isValidToGetNewOtp($logOtp, $verificationId);
            } catch (NoSuchEntityException $e) {
                //create new otp code
            }
        }

        try {
            if($isValid) {
                $logOtp = $this->logOtp->create();
                $logOtp->setVerificationId($this->fileHelper->generateUUID());
                $logOtp->setOtpCode($this->tcastOTPSenderHelper->getConfigHelper()->getOTPCode());
                $logOtp->setMobileNumber($telephone);
                $logOtp->setDelivered(0);
                $logOtp->setVerified(0);
                $this->logOtpRepository->save($logOtp);
            }
            $result['status'] = true;
            $result['verification_id'] = $logOtp->getVerificationId();
            $result['code'] = $logOtp->getOtpCode();
            $result['is_diff'] = $isValid;
        } catch (\Exception $e) {
            $result['status'] = false;
        }

        return $this->jsonHelper->serializeJson($result);
    }

    /**
     * save API response
     *
     * @param string $verificationCode
     * @param string $response
     * @return void
     */
    protected function saveResponses(string $verificationId, $responses = [])
    {
        if($verificationId) {
            try {
                $otp = $this->logOtpRepository->get($verificationId);
                $otp->setMessageId($responses['messageId']);
                $otp->setDeliveryRequestResp($responses['sendResponse']);
                $otp->setDeliveryStatusResp($responses['statusResponse']);
                $delivered = $responses['delivered'] ? LogOtpInterface::STATUS_SUCCESS : LogOtpInterface::STATUS_FAILED;
                $otp->setDelivered($delivered);
                $this->logOtpRepository->save($otp);
            } catch (NoSuchEntityException $e) {
                //no data found
            }
        }
    }

    /**
     * set verified
     *
     * @param string $verificationId
     * @return void
     */
    protected function setOtpVerified(string $verificationId)
    {
        if($verificationId) {
            try {
                $logOtp = $this->logOtpRepository->get($verificationId);
                $logOtp->setVerified(1);
                $this->logOtpRepository->save($logOtp);
            } catch (NoSuchEntityException $e) {
                //no data found
            }
        }
    }

    /**
     * Check interval time
     *
     * @param \CTCD\TCastSMS\Api\Data\LogOtpInterface $logOtp
     * @param string $verificationId
     * @return bool
     */
    protected function isValidToGetNewOtp(
        \CTCD\TCastSMS\Api\Data\LogOtpInterface $logOtp, 
        string $verificationId = null
    ){
        if(!$this->tcastOTPSenderHelper->getConfigHelper()->isTimeIntervalEnabled()) {
            return true;
        }
        $interval = $this->tcastOTPSenderHelper->getConfigHelper()->getOTPTimeInterval();
        $diff = strtotime($this->dateTime->gmtDate('Y-m-d H:i:s')) - strtotime($logOtp->getCreatedAt());
        if($verificationId && $diff > $interval) {
            return true;
        }

        return false;
    }

    /**
     * Check code active
     *
     * @param \CTCD\TCastSMS\Api\Data\LogOtpInterface $logOtp
     * @return bool
     */
    protected function isOtpStillValid(
        \CTCD\TCastSMS\Api\Data\LogOtpInterface $logOtp
    ){
        $otpLifetime = $this->tcastOTPSenderHelper->getConfigHelper()->getOTPLifetime() * 60;
        $diff = strtotime($this->dateTime->gmtDate('Y-m-d H:i:s')) - strtotime($logOtp->getCreatedAt());
        if($diff > $otpLifetime) {
            return false;
        }

        return true;
    }
}
