<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Helper;

/**
 * Class Config
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * config path
	 */
	const ENABLE_MODULE = 'category_importer/general/active';
    const ROOT_CATEGORY_ID = 'category_importer/general/root_category_id';

	/**
	 * get config value
	 *
	 * @param string $path
	 * @return string
	 */
	public function getConfigValue($path)
	{
		if ($path) {
			return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		}
	}

	/**
	 * is payment enable
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return (bool) $this->scopeConfig->getValue(self::ENABLE_MODULE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * get widget limit
	 *
	 * @return int
	 */
	public function getRootCategoryId()
	{
		return (int) $this->scopeConfig->getValue(self::ROOT_CATEGORY_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}
