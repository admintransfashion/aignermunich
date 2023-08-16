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

namespace Trans\Catalog\Plugin\Directory\Model;

use Magento\Directory\Model\PriceCurrency as ProductPriceCurrency;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Plugin class PriceCurrency
 */
class PriceCurrency
{
	/**
	 * remove decimal on price
	 * 
	 * @param ProductPriceCurrency $subject
	 * @param callable $proceed
	 * @param $amount
	 * @param bool $includeContainer
	 * @param int $precision
	 * @param mixed $scope
	 * @param mixed $currency
	 * @return ProductPriceCurrency
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	public function aroundFormat(
		ProductPriceCurrency $subject, callable $proceed,
		$amount,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION,
        $scope = null,
        $currency = null
	)
	{
		$precision = 0;
		
		return $proceed($amount, $includeContainer, $precision, $scope, $currency);
	}
}