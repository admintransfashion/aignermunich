<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Model\Quote\Cart;

use Trans\LocationCoverage\Api\Quote\ShippingMethodManagementInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Model\Cart\TotalsAdditionalDataProcessor;
use Magento\Quote\Model\Cart\CartTotalManagement as MagentoQuoteCartTotalManagement;

/**
 * @inheritDoc
 */
class CartTotalManagement extends MagentoQuoteCartTotalManagement
{
    /**
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param CartTotalRepositoryInterface $cartTotalsRepository
     * @param TotalsAdditionalDataProcessor $dataProcessor
     */
    public function __construct(
        ShippingMethodManagementInterface $shippingMethodManagement,
        PaymentMethodManagementInterface $paymentMethodManagement,
        CartTotalRepositoryInterface $cartTotalsRepository,
        TotalsAdditionalDataProcessor $dataProcessor
    ) {
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->paymentMethodManagement  = $paymentMethodManagement;
        $this->cartTotalsRepository     = $cartTotalsRepository;
        $this->dataProcessor            = $dataProcessor;
    }
}