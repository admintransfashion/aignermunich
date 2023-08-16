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

namespace CTCD\TCastSMS\Api;

use CTCD\TCastSMS\Api\Data\LogOtpInterface;

/**
 * @api
 *
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
interface LogOtpRepositoryInterface
{
    /**
     * Save data
     *
     * @param \CTCD\TCastSMS\Api\Data\LogOtpInterface $parameters
     * @return \CTCD\TCastSMS\Api\Data\LogOtpInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(LogOtpInterface $parameters);

    /**
     * Get data
     *
     * @param string $key
     * @param bool $forceReload
     * @return \CTCD\TCastSMS\Api\Data\LogOtpInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($key, $forceReload = false);

    /**
     * Delete data
     *
     * @param string $key
     * @return \CTCD\TCastSMS\Api\Data\LogOtpInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete($key);

}
