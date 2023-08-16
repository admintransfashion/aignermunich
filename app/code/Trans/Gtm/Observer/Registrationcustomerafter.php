<?php

/**
 * @category Trans
 * @package  Trans_Gtm
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   ashadi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Gtm\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Registrationcustomerafter
 */
class Registrationcustomerafter implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\RequestInterface $request
     */

	function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\RequestInterface $request
    )

    {
        $this->customerSession = $customerSession;
        $this->request = $request;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getCustomer();
        $dataLayerRegister = [];
        if($customer && $customer->getId()){
            $dataLayerRegister = [
                'event' => 'register',
                'prefix' => '',
                'first_name' => hash('sha256', $customer->getFirstname()),
                'last_name' => hash('sha256', $customer->getLastname()),
                'email' => hash('sha256', $customer->getEmail()),
                'checkbox_agreement' => 'Yes'
            ];
        }
        $this->customerSession->setDataLayerRegister($dataLayerRegister);
        return $this;
    }
}
