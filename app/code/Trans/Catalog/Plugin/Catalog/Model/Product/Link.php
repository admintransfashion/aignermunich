<?php
/**
 * @category Trans
 * @package  Trans_Catalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Plugin\Catalog\Model\Product;

use Magento\Catalog\Model\Product\Link as ProductLink;

/**
 * Plugin class Link
 */
class Link
{
	/**
	 * @var \Magento\Eav\Model\Entity\Attribute
	 */
	protected $eavAttribute;

	/**
	 * @var \Trans\Reservation\Helper\Config
	 */
	protected $configHelper;

	/**
	 * @param \Magento\Eav\Model\Entity\Attribute $eavAttribute
	 * @param \Trans\Reservation\Helper\Config $configHelper
	 */
	public function __construct(
		\Magento\Eav\Model\Entity\Attribute $eavAttribute,
		\Trans\Catalog\Helper\Config $configHelper
	)
	{
		$this->eavAttribute = $eavAttribute;
		$this->configHelper = $configHelper;
	}

	/**
	 * add season filter
	 * 
	 * @param ProductLayer $subject
	 * @param callable $result
	 * @return $this
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	public function afterGetProductCollection(ProductLink $subject, $result)
	{
		if($this->configHelper->isEnabledSeason())
		{
			$season = $this->configHelper->getSelectedSeason();
			if($season != 'all') {
				$result->addAttributeToFilter('season', $season);
			}
		}


		return $result;
	}
}