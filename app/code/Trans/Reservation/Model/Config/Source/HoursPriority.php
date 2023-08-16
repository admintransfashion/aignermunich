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

use Trans\Reservation\Api\Data\ReservationConfigInterface;

/**
 * Class HoursPriority
 */
class HoursPriority implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function toOptionArray()
	{
		return [
            ['value' => ReservationConfigInterface::CONFIG_PRIORITY_LONGEST, 'label' => __('Longest')],
            ['value' => ReservationConfigInterface::CONFIG_PRIORITY_SHORTEST, 'label' => __('Shortest')],
            ['value' => ReservationConfigInterface::CONFIG_PRIORITY_LATEST, 'label' => __('Latest')]
        ];
	}
}

?>
