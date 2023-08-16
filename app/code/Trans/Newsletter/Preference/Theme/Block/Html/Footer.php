<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Trans\Newsletter\Preference\Theme\Block\Html;

use Magento\Theme\Block\Html\Footer as MageFooter;
use Magento\Customer\Model\Context;

/**
 * Html page footer block
 *
 * @api
 * @since 100.0.2
 */
class Footer extends MageFooter
{
    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', ['_secure' => true]);
    }
}
