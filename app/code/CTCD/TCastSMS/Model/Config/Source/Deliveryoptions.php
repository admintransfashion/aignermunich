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

namespace CTCD\TCastSMS\Model\Config\Source;

use CTCD\TCastSMS\Api\Data\LogOtpInterface;

class Deliveryoptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => LogOtpInterface::STATUS_FAILED, 'label' => __('Failed')], ['value' => LogOtpInterface::STATUS_SUCCESS, 'label' => __('Success')], ['value' => LogOtpInterface::STATUS_PENDING, 'label' => __('Pending')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [LogOtpInterface::STATUS_PENDING => __('Pending'), LogOtpInterface::STATUS_SUCCESS => __('Success'), LogOtpInterface::STATUS_FAILED => __('Failed')];
    }
}
