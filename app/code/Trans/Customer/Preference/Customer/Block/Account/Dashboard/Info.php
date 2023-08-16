<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Customer
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Customer\Preference\Customer\Block\Account\Dashboard;

class Info extends \Magento\Customer\Block\Account\Dashboard\Info
{
    public function getTelephone()
    {
        $customer = $this->getCustomer();
        if($customer && $customer->getId()){
            return $customer->getCustomAttribute('telephone')->getValue();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getCustomChangePasswordUrl()
    {
        return $this->_urlBuilder->getUrl('customer/account/changepassword');
    }
}
