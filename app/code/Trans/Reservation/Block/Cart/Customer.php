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

namespace Trans\Reservation\Block\Cart;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Customer
 */
class Customer extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
	 * @var \Trans\Reservation\Helper\Data
	 */
	protected $dataHelper;

	/**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $datetime;

    /**
     * @var logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Helper\Reservation $reservationHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Url $customerUrl,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Reservation $reservationHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->customerUrl = $customerUrl;
    	$this->reservationHelper = $reservationHelper;

    	$this->storeManager = $dataHelper->getStoreManager();
        $this->datetime = $dataHelper->getDatetime();
        $this->logger = $dataHelper->getLogger();

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * get login post url
     *
     * @return string
     */
    public function getLoginUr()
    {
        return $this->customerUrl->getLoginPostUrl();
    }

    /**
     * is customer logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->dataHelper->isCustomerLoggedIn();
    }

    /**
     * Get customer data
     *
     * @return Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomerData()
    {
        return $this->dataHelper->getCustomerData();
    }

    /**
     * get forgot password url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->getUrl('customer/account/forgotpassword/');
    }
}
