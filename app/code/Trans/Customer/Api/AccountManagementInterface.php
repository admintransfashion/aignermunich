<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Api;

use Magento\Framework\Exception\InputException;

/**
 * Interface for managing customers accounts.
 * @api
 */
interface AccountManagementInterface
{
    /**
     * send sms verification.
     *
     * @param string $telephone
     * @param string $verificationId
     * @param int $isAjax
     * @param string $key
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendSmsOtp(string $telephone, string $verificationId = null, $isAjax = 0, string $key = null);

    /**
     * Verify OTP
     *
     * @param string $code
     * @param string $verificationId
     * @param int $websiteId
     * @param string $key
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function verifySmsOtp(string $code, string $verificationId, $websiteId = null, string $key = null);

    /**
     * Get OTP auth code.
     *
     * @param string $telephone
     * @param string $verificationId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOtpAuthCode(string $telephone, string $verificationId = null);
}
