<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_CustomHeadBlock
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\CustomHeadBlock\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
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
        return (bool) $this->getConfigValue('ctcd_customheadblock/general/active');
    }

    /**
     * Is facebook meta name enable
     *
     * @return bool
     */
    public function isFacebookEnabled()
    {
        return (bool) $this->getConfigValue('ctcd_customheadblock/facebook/active');
    }

    /**
     * Get facebook meta name
     *
     * @return string
     */
    public function getFacebookMetaName()
    {
        $label = $this->getConfigValue('ctcd_customheadblock/facebook/meta_name');
        return $label ? $label : '';
    }

    /**
     * Get facebook meta value
     *
     * @return string
     */
    public function getFacebookMetaValue()
    {
        $value = $this->getConfigValue('ctcd_customheadblock/facebook/meta_value');
        return $value ? $value : '';
    }


}
