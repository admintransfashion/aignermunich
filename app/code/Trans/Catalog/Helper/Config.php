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

namespace Trans\Catalog\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Constant config path
     */
    const ENABLE_SEASON = 'catalog/catalog_season/enabled';
    const SELECTED_SEASON = 'catalog/catalog_season/season_selected';

    /**
     * Get config value by path
     * 
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * is module enabled
     * 
     * @return bool
     */
    public function isEnabledSeason()
    {
        return $this->getConfigValue(self::ENABLE_SEASON);
    }

    /**
     * get selected season
     * 
     * @return string
     */
    public function getSelectedSeason()
    {
        return $this->getConfigValue(self::SELECTED_SEASON);
    }   
}