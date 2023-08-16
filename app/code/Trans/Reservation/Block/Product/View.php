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

namespace Trans\Reservation\Block\Product;

/**
 * Class View
 */
class View extends \Magento\Catalog\Block\Product\View
{
	/**
	 * is product available to reserve
	 *
	 * @return bool
	 */
	public function isReserveAble()
	{
		$block = $this->getLayout()->createBlock('\Trans\Reservation\Block\Product\View\Reservation');
		$sources = $block->getProductSourcesArray();

		if(!empty($sources)) {
			return true;
		}

		return false;
	}
}
