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
namespace Trans\Gtm\Preference\Search\Helper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Search\Model\Query as SearchQuery;
use Magento\Search\Model\QueryFactory;

/**
 * Search helper
 * @api
 * @since 100.0.2
 */
class Data extends \Magento\Search\Helper\Data
{
    /**
     * Note messages
     *
     * @var array
     */
    protected $_messages = [];

    /**
     * Magento string lib
     *
     * @var String
     */
    protected $string;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     * @since 100.1.0
     */
    protected $scopeConfig;

    /**
     * @var Escaper
     * @since 100.1.0
     */
    protected $escaper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     * @since 100.1.0
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     * @since 100.1.0
     */
    protected $customerSession;

    /**
     * Construct
     *
     * @param Context $context
     * @param StringUtils $string
     * @param Escaper $escaper
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        StringUtils $string,
        Escaper $escaper,
        StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession; 
        parent::__construct(
            $context,
            $string,
            $escaper,
            $storeManager
        );
    }

    /**
     * @return string
     */
    public function getCustomerIdCustom()
    {
        //return current customer ID
        if ($this->customerSession->getCustomer()->getEmail()) {
            $hashcustomeremail = hash('sha256', $this->customerSession->getCustomer()->getEmail());
            return $hashcustomeremail;
        }
        return false;
    }
}
