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

namespace Trans\Catalog\Plugin\Catalog\Model;

use Magento\Catalog\Model\Layer as ProductLayer;

/**
 * Plugin class Layer
 */
class Layer
{
	/**
	 * @var \Magento\Eav\Model\Entity\Attribute
	 */
	protected $eavAttribute;

	/**
	 * @var \Magento\Catalog\Block\Product\ProductList\Toolbar
	 */
	protected $toolbar;

	/**
	 * @var \Trans\Reservation\Helper\Config
	 */
	protected $configHelper;

	/**
	 * @param \Magento\Eav\Model\Entity\Attribute $eavAttribute
	 * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
	 * @param \Trans\Reservation\Helper\Config $configHelper
	 */
	public function __construct(
		\Magento\Eav\Model\Entity\Attribute $eavAttribute,
		\Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar,
		\Trans\Catalog\Helper\Config $configHelper
	)
	{
		$this->toolbar = $toolbar;
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
	public function afterGetProductCollection(ProductLayer $subject, $result)
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

	/**
     * Initialize product collection
     *
	 * @param ProductLayer $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return void
     */
	public function beforePrepareProductCollection(ProductLayer $subject, $collection): void
	{
		/** @var \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar */
	    $toolbar = $this->toolbar;

	    switch ($toolbar->getCurrentOrder()) {
	        case 'season':
	            $collection->addAttributeToSort('season', 'desc');

	            break;
	    }
	}

}