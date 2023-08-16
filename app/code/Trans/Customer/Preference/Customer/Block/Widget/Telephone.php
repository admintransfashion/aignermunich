<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Customer
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Customer\Preference\Customer\Block\Widget;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Customer\Model\Options;
use Magento\Framework\View\Element\Template\Context;

class Telephone extends \Magento\Customer\Block\Widget\Telephone
{
    /**
     * @var \CTCD\TCastSMS\Helper\Data
     */
    protected $tcastConfigHelper;

    /**
     * Telephone constructor.
     * @param Context $context
     * @param AddressHelper $addressHelper
     * @param CustomerMetadataInterface $customerMetadata
     * @param Options $options
     * @param AddressMetadataInterface $addressMetadata
     * @param \CTCD\TCastSMS\Helper\Data $tcastConfigHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        AddressHelper $addressHelper,
        CustomerMetadataInterface $customerMetadata,
        Options $options,
        AddressMetadataInterface $addressMetadata,
        \CTCD\TCastSMS\Helper\Data $tcastConfigHelper,
        array $data = []
    ) {
        $this->tcastConfigHelper = $tcastConfigHelper;
        parent::__construct($context, $addressHelper, $customerMetadata, $options, $addressMetadata, $data);
    }

    public function isOtpEnable()
    {
        return $this->tcastConfigHelper->isModuleEnabled();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('customer/account/changetelephonepost', ['_secure' => true]);
    }
}
