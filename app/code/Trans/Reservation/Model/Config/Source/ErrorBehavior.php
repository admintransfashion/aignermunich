<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Reservation
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Reservation\Model\Config\Source;

/**
 * Class ErrorBehavior
 */
class ErrorBehavior implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * const for import behavior
     */
    const STOP = 'stop';
    const SKIP = 'skip';

    /**
     * prepare option
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STOP, 'label' => __('Stop on error')],
            ['value' => self::SKIP, 'label' => __('Skip error entries')],
        ];
    }
}
