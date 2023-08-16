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

namespace CTCD\TCastSMS\Api\Data;

/**
 * @api
 */
interface LogOtpInterface
{
    const ENTITY_ID = 'entity_id';
    const VERIFICATION_ID = 'verification_id';
    const MESSAGE_ID = 'message_id';
    const OTP_CODE = 'otp_code';
    const MOBILE_NUMBER = 'mobile_number';
    const DELIVERED = 'delivered';
    const VERIFIED = 'verified';
    const DELIVERY_REQUEST_RESPONSE = 'delivery_request_resp';
    const DELIVERY_STATUS_RESPONSE = 'delivery_status_resp';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const TABLE_NAME = 'tcast_otp';

    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;

    /**
     * Get verification ID
     *
     * @return string
     */
    public function getVerificationId();

    /**
     * Get message ID
     *
     * @return string
     */
    public function getMessageId();

    /**
     * Get OTP code
     *
     * @return string
     */
    public function getOTPCode();

    /**
     * Get mobile number
     *
     * @return string
     */
    public function getMobileNumber();

    /**
     * Get status of OTP is delivered to customer or not
     * 0: In Process, 1: Delivered, 2: Not Delivered
     *
     * @return int
     */
    public function getDelivered();

    /**
     * Get the status whether the customer has verified OTP or not
     * 0: Unverified, 1: Verified
     *
     * @return int
     */
    public function getVerified();

    /**
     * Get response of OTP delivery request
     *
     * @return string
     */
    public function getDeliveryRequestResp();

    /**
     * Get response from OTP delivery status
     *
     * @return string
     */
    public function getDeliveryStatusResp();

    /**
     * Set verification ID
     *
     * @param string $verificationId
     * @return this
     */
    public function setVerificationId($verificationId);

    /**
     * Set message ID
     *
     * @param string $messageId
     * @return this
     */
    public function setMessageId($messageId);

    /**
     * Set OTP code
     *
     * @param string $otpCode
     * @return this
     */
    public function setOTPCode($otpCode);

    /**
     * Set mobile number
     *
     * @param string $mobileNumber
     * @return this
     */
    public function setMobileNumber($mobileNumber);

    /**
     * Set status of OTP is delivered to customer or not
     * 0: In Process, 1: Delivered, 2: Not Delivered
     *
     * @param int $status
     * @return this
     */
    public function setDelivered($status);

    /**
     * Set the status whether the customer has verified OTP or not
     * 0: Unverified, 1: Verified
     *
     * @param int $status
     * @return this
     */
    public function setVerified($status);

    /**
     * Set response of OTP delivery request
     *
     * @param string $response
     * @return this
     */
    public function setDeliveryRequestResp($response);

    /**
     * Set response from OTP delivery status
     *
     * @param string $response
     * @return this
     */
    public function setDeliveryStatusResp($response);

    /**
     * set Date Created
     *
     * @param string $date
     * @return $this
     */
    public function setCreatedAt($date);

    /**
     * Set Updated Date
     *
     * @param string $date
     * @return $this
     */
    public function setUpdatedAt($date);
}
