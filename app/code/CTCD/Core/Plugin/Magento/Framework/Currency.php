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

namespace CTCD\Core\Plugin\Magento\Framework;

class Currency extends \CTCD\Core\Plugin\PluginAbstract
{
    /**
     * @inheritdoc
     */
    public function beforeToCurrency(
        \Magento\Framework\Currency $subject,
        $value = null,
        $options = []
    ){
        if ($this->ctcdCoreHelper->isDecimalPriceFeatureEnabled()) {
            $precision = $this->ctcdCoreHelper->isShowDecimalOnPrice() ? $this->ctcdCoreHelper->getDecimalPricePrecision() : 0;
            $options['precision'] = $precision;
        }
        return [$value, $options];
    }
}
