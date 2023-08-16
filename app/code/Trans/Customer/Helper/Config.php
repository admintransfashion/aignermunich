<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Helper;

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
    const ENABLE_LOGIN_PHONE = 'customer/startup/enabled_phone_login';
    
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
     * is sales disabled
     * 
     * @return bool
     */
    public function isPhoneLoginEnabled()
    {
        return $this->getConfigValue(self::ENABLE_LOGIN_PHONE);
    }

    /**
     * get add to cart template
     * 
     * @return string
     */
    public function getLoginTemplate()
    {
        $template = 'Magento_Customer::form/login.phtml';
        
        if($this->isPhoneLoginEnabled()) {
            $template = 'Trans_Customer::form/login.phtml';
        }

        return $template;
    }   
}