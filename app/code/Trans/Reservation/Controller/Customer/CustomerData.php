<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Reservation
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Reservation\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Sourcedata
 */
class Sourcedata extends Action
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerFactory;

    /**
     * @var \Trans\Customer\Helper\Config
     */
    protected $configHelper;

    /**
     * @param Context $context
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
     * @param \Trans\Customer\Helper\Config $configHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Trans\Customer\Helper\Config $configHelper
    )
    {
        $this->request = $request;
        $this->customerFactory = $customerFactory;
        $this->configHelper = $configHelper;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $username = $this->request->getParam('username');
        if($username && !strpos($username, '@') !== false )
        {
            /* Get email id based on mobile number and login*/
            $customereCollection = $this->customerFactory->create();
            $customereCollection->addFieldToFilter("telephone", $username);

            if($customereCollection->getSize() > 0) {
                return true;
            }
        }
    }
}
