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

namespace CTCD\Core\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class RedirectType implements ArrayInterface
{
    const RESPONSE_BLANK = 0;
    const RESPONSE_REDIRECT_HOMEPAGE = 1;
    const RESPONSE_REDIRECT_404 = 2;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Return array of dates
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->options[] = ['label' => __('Blank Page'), 'value' => self::RESPONSE_BLANK];
        $this->options[] = ['label' => __('Redirect to Homepage'), 'value' => self::RESPONSE_REDIRECT_HOMEPAGE];
        $this->options[] = ['label' => __('Redirect to 404 Page'), 'value' => self::RESPONSE_REDIRECT_404];

        return $this->options;
    }
}
