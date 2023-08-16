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

namespace CTCD\Core\Plugin\Magento\Sales\Model;

class Order extends \CTCD\Core\Plugin\PluginAbstract
{
    /**
     * @inheritdoc
     */
    public function beforeFormatPricePrecision(
        \Magento\Sales\Model\Order $subject,
        $price,
        $precision,
        $addBrackets = false
    ) {
        if ($this->ctcdCoreHelper->isDecimalPriceFeatureEnabled()) {
            $precision = $this->ctcdCoreHelper->isShowDecimalOnPrice() ? $this->ctcdCoreHelper->getDecimalPricePrecision() : 0;
        }
        return [$price, $precision, $addBrackets];
    }
}
