<?php

/**
 * @category CTCD
 * @package  CTCD_Core
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace CTCD\Core\Model\Config\Source\Form\Field;

class Precision implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of decimal precisions
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('1')],
            ['value' => 2, 'label' => __('2')],
            ['value' => 3, 'label' => __('3')],
            ['value' => 4, 'label' => __('4')],
        ];
    }
}
