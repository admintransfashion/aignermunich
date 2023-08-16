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

namespace CTCD\TCastSMS\Model;

use Magento\Framework\Model\AbstractModel;
use CTCD\TCastSMS\Api\Data\LogOtpInterface;
use CTCD\TCastSMS\Model\ResourceModel\LogOtp as LogOtpResourceModel;

class LogOtp extends AbstractModel implements LogOtpInterface
{
    /**
     * @var string
     */
    const CACHE_TAG = 'tcastotplog';

    /**
     * @var string
     */
    protected $_eventPrefix = self::CACHE_TAG;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LogOtpResourceModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getVerificationId()
    {
        return (string) $this->getData(LogOtpInterface::VERIFICATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageId()
    {
        return (string) $this->getData(LogOtpInterface::MESSAGE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getOTPCode()
    {
        return (string) $this->getData(LogOtpInterface::OTP_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getMobileNumber()
    {
        return (string) $this->getData(LogOtpInterface::MOBILE_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function getDelivered()
    {
        return (int) $this->getData(LogOtpInterface::DELIVERED);
    }

    /**
     * {@inheritdoc}
     */
    public function getVerified()
    {
        return (int) $this->getData(LogOtpInterface::VERIFIED);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryRequestResp()
    {
        return (string) $this->getData(LogOtpInterface::DELIVERY_REQUEST_RESPONSE);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryStatusResp()
    {
        return (string) $this->getData(LogOtpInterface::DELIVERY_STATUS_RESPONSE);
    }

    /**
     * {@inheritdoc}
     */
    public function setVerificationId($verificationId)
    {
        $this->setData(LogOtpInterface::VERIFICATION_ID, $verificationId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageId($messageId)
    {
        $this->setData(LogOtpInterface::MESSAGE_ID, $messageId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOTPCode($otpCode)
    {
        $this->setData(LogOtpInterface::OTP_CODE, $otpCode);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->setData(LogOtpInterface::MOBILE_NUMBER, $mobileNumber);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDelivered($status)
    {
        $this->setData(LogOtpInterface::DELIVERED, $status);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setVerified($status)
    {
        $this->setData(LogOtpInterface::VERIFIED, $status);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveryRequestResp($response)
    {
        $this->setData(LogOtpInterface::DELIVERY_REQUEST_RESPONSE, $response);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveryStatusResp($response)
    {
        $this->setData(LogOtpInterface::DELIVERY_STATUS_RESPONSE, $response);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($date)
    {
        $this->setData(LogOtpInterface::CREATED_AT, $date);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($date)
    {
        $this->setData(LogOtpInterface::UPDATED_AT, $date);
        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
