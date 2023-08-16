<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const DEFAULT_MENU_LABEL = 'Inspiration';

    /* Inspiration used tables */
    const INSPIRATION_TABLE = 'ctcd_inspiration';

    /**
     * @param string $field
     * @param string|null $scope
     * @param int|null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $scope = null, $storeId = null)
    {
        $scope = !$scope ? ScopeInterface::SCOPE_WEBSITE : $scope;
        return $this->scopeConfig->getValue(
            $field,
            $scope,
            $storeId
        );
    }

    /**
     * Is module enabled?
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return (bool) $this->getConfigValue('ctcd_inspiration/general/active');
    }

    /**
     * Is add the inspiration to topmenu
     *
     * @return bool
     */
    public function isAddedToTopmenu()
    {
        return (bool) $this->getConfigValue('ctcd_inspiration/topmenu/active');
    }

    /**
     * Get label for menu
     *
     * @return string
     */
    public function getMenuLabel()
    {
        $label = $this->getConfigValue('ctcd_inspiration/topmenu/parent_label');
        return $label ? $label : self::DEFAULT_MENU_LABEL;
    }

    /**
     * add child to menu
     *
     * @return bool
     */
    public function addChildToMenu()
    {
        return (bool) $this->getConfigValue('ctcd_inspiration/topmenu/include_content');
    }
}
