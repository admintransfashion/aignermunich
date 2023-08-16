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

namespace CTCD\Core\Plugin\Magento\Framework\Locale;

class Format extends \CTCD\Core\Plugin\PluginAbstract
{
    /**
     * @inheritdoc
     */
    public function afterGetPriceFormat(
        \Magento\Framework\Locale\Format $subject,
        $result
    ){
        if ($this->ctcdCoreHelper->isDecimalPriceFeatureEnabled()) {
            $precision = $this->ctcdCoreHelper->isShowDecimalOnPrice() ? $this->ctcdCoreHelper->getDecimalPricePrecision() : 0;
            $result['precision'] = $precision;
            $result['requiredPrecision'] = $precision;
        }
        return $result;
    }
}
