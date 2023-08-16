<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Core\Helper;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $appHttpContext;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $customerGroup;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $customerGroupCollection;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $customerGroupCollectionFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Http\Context $appHttpContext
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Group $customerGroup
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $appHttpContext,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Group $customerGroup,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->appHttpContext = $appHttpContext;
        $this->customerSession = $customerSession;
        $this->customerGroup = $customerGroup;
        $this->customerGroupCollection = $customerGroupCollection;
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;

        parent::__construct($context);
    }

    /**
     * Get customer repository object
     *
     * @return \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }

    /**
     * Get customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerSession()
    {
        return $this->customerSession;
    }

    /**
     * Check whether the customer logged in or not
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->appHttpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH) || $this->customerSession->isLoggedIn();
    }

    /**
     * Get customer from current session
     *
     * @return mixed
     */
    public function getCurrentCustomer()
    {
        return $this->isLoggedIn() ? $this->customerSession->getCustomer() : null;
    }

    /**
     * Get customer group options
     *
     * @return array
     */
    public function getCustomerGroupOptions()
    {
        $customerGroups = $this->customerGroupCollection->toOptionArray();
        array_unshift($customerGroups, array('value'=>'', 'label'=>'Any'));
        return $customerGroups;
    }

    /**
     * Get all customer group ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        $customerGroupIds = [];
        foreach ($this->customerGroupCollection as $customerGroup) {
            $customerGroupIds[] = $customerGroup->getId();
        }
        return $customerGroupIds;
    }

    /**
     * Get all customer group codes
     *
     * @return array
     */
    public function getCustomerGroupCodes()
    {
        $customerGroupCodes = [];
        foreach ($this->customerGroupCollection as $customerGroup) {
            $customerGroupCodes[] = $customerGroup->getCode();
        }
        return $customerGroupCodes;
    }

    /**
     * Get all customer group ids & codes
     *
     * @return array
     */
    public function getCustomerGroupIdCodes()
    {
        $customerGroupCodes = [];
        foreach ($this->customerGroupCollection as $customerGroup) {
            $customerGroupCodes[$customerGroup->getId()] = $customerGroup->getCode();
        }
        return $customerGroupCodes;
    }

    /**
     * Get Current Customer Group Id
     *
     * @return int
     */
    public function getCurrentCustomerGroupId()
    {
        return $this->getCurrentCustomer() ? $this->getCurrentCustomer()->getGroupId() : 0;
    }

    /**
     * Get current customer group name
     *
     * @return string
     */
    public function getCurrentCustomerGroupName()
    {
        $model = $this->customerGroup->load($this->getCurrentCustomerGroupId());
        return $model->getCustomerGroupCode();
    }

    /**
     * Get Customer with custom attributes data
     *
     * @return mixed
     */
    public function getCustomerWithAttributes()
    {
        return $this->getCurrentCustomer() ? $this->customerRepository->getById($this->getCurrentCustomer()->getId()) : null;
    }

    /**
     * Get Customer group collection
     *
     * @return \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    public function getCustomerGroupCollection()
    {
        return $this->customerGroupCollectionFactory->create();
    }

    /**
     * Get customer by email
     *
     * @param string $email
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer($email)
    {
        $customer = null;
        if($email) {
            try {
                $customer = $this->customerRepository->get($email);
            } catch (\Exception $e) {
            }
        }
        return $customer;
    }

    /**
     * Get customer by email
     *
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomerById($customerId)
    {
        $customer = null;
        if($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
            } catch (\Exception $e) {
            }
        }
        return $customer;
    }

    /**
     * Load customer by id
     *
     * @param int $customerId
     * @return \Magento\Customer\Model\Customer|null
     */
    public function loadCustomer($customerId)
    {
        $customer = null;
        if($customerId) {
            try {
                $customer = $this->customerFactory->create()->load($customerId);
            } catch (\Exception $e) {
            }
        }
        return $customer;
    }

    /**
     * Set force logged in by customer id
     *
     * @param int $customerId
     * @return mixed|null
     */
    public function setForceLoggedInById($customerId)
    {
        $customer = null;
        if($customerId) {
            if($this->customerSession->loginById($customerId)){
                $customer = $this->getCurrentCustomer();
            }
        }
        return $customer;
    }

    /**
     * set force logged in by email
     *
     * @param string $email
     * @return mixed|null
     */
    public function setForceLoggedInByEmail($email)
    {
        $customer = null;
        if($email) {
            $customerRepo = $this->getCustomer($email);
            if($customerRepo && $customerRepo->getId()){
                $customer = $this->setForceLoggedInById($customerRepo->getId());
            }
        }
        return $customer;
    }
}
