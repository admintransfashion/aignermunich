<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Core\Plugin\Magento\Directory\Model;

class PriceCurrency extends \CTCD\Core\Plugin\PluginAbstract
{
    /**
     * @inheritdoc
     */
    public function beforeFormat(
        \Magento\Directory\Model\PriceCurrency $subject,
        $amount,
        $includeContainer = true,
        $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
        $scope = null,
        $currency = null
    ) {
        if ($this->ctcdCoreHelper->isDecimalPriceFeatureEnabled()) {
            if (!isset($includeContainer)) {
                $includeContainer = true;
            }
            $precision = $this->ctcdCoreHelper->isShowDecimalOnPrice() ? $this->ctcdCoreHelper->getDecimalPricePrecision() : 0;
        }
        return [$amount, $includeContainer, $precision, $scope, $currency];
    }

    /**
     * @inheritdoc
     */
    public function aroundRound(
        \Magento\Directory\Model\PriceCurrency $subject,
        callable $proceed,
        $price
    ) {
        if ($this->ctcdCoreHelper->isDecimalPriceFeatureEnabled()) {
            $precision = $this->ctcdCoreHelper->isShowDecimalOnPrice() ? $this->ctcdCoreHelper->getDecimalPricePrecision() : 0;
            return round($price, $precision);
        }
        return $proceed($price);
    }

    /**
     * @inheritdoc
     */
    public function beforeConvertAndFormat(
        \Magento\Directory\Model\PriceCurrency $subject,
        $amount,
        $includeContainer = true,
        $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
        $scope = null,
        $currency = null
    ) {
        if ($this->ctcdCoreHelper->isDecimalPriceFeatureEnabled()) {
            $includeContainer = isset($includeContainer) ? $includeContainer : null;
            $customPrecision = $this->ctcdCoreHelper->isShowDecimalOnPrice() ? $this->ctcdCoreHelper->getDecimalPricePrecision() : 0;
            $precision = intval($customPrecision);
        }
        return [$amount, $includeContainer, $precision, $scope, $currency];
    }

    /**
     * @inheritdoc
     */
    public function beforeConvertAndRound(
        \Magento\Directory\Model\PriceCurrency $subject,
        $amount,
        $scope = null,
        $currency = null,
        $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        if ($this->ctcdCoreHelper->isDecimalPriceFeatureEnabled()) {
            $scope = isset($scope) ? $scope : null;
            $currency = isset($currency) ? $currency : null;
            $precision = $this->ctcdCoreHelper->isShowDecimalOnPrice() ? $this->ctcdCoreHelper->getDecimalPricePrecision() : 0;
        }
        return [$amount, $scope, $currency, $precision];
    }
}
