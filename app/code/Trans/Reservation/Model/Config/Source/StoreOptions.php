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
 * Class StoreOptions
 */
class StoreOptions implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
	 */
	protected $searchCriteriaBuilder;

	/**
	 * @var \Trans\Reservation\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilder
	 * @param \Trans\Reservation\Helper\Data $dataHelper
	 */
	public function __construct(
		\Trans\Reservation\Helper\Data $dataHelper
	)
	{
		$this->dataHelper = $dataHelper;
	}

	/**
	 * {@inheritdoc}
	 */
	public function toOptionArray()
	{
		$sources = $this->dataHelper->getSources();

		foreach($sources as $source) {
			$data[] = ['value' => $source->getSourceCode(), 'label' => $source->getName()];
		}

		return $data;
	}
}

?>
