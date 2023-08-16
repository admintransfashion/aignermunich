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
 * Class ImportBehavior
 */
class ImportBehavior implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * const for import behavior
     */
    const ADD_UPDATE = 'add_update';
    const DELETE = 'delete';

    /**
     * prepare option
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ADD_UPDATE, 'label' => __('Add & Update')],
            ['value' => self::DELETE, 'label' => __('Delete')],
        ];
    }
}
