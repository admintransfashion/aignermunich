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

namespace Trans\Reservation\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Constant config path
     */
    const ENABLE_MODULE = 'reservation/general/enabled';
    const DISABLE_SALES = 'reservation/general/disable_sales';

    /**
     * sql condition
     */
    const LESS_THAN = 'less_than';
    const GREATER_THAN = 'greater_than';

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $datetime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemRepositoryInterface
     */
    protected $sourceItemRepository;

    /**
     * @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface
     */
    protected $getSourceItem;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    protected $regionCollection;

    /**
     * @var \Trans\Reservation\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Trans\Reservation\Api\SourceAttributeRepositoryInterface
     */
    protected $sourceAttrRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItem
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollection
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Trans\Reservation\Api\SourceAttributeRepositoryInterface $sourceAttrRepository
     * @param \Trans\Reservation\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItem,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollection,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Trans\Reservation\Api\SourceAttributeRepositoryInterface $sourceAttrRepository,
        \Trans\Reservation\Logger\Logger $logger
    )
    {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->datetime = $datetime;
        $this->timezone = $timezone;
        $this->getSourceItem = $getSourceItem;
        $this->sourceRepository = $sourceRepository;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->configurable = $configurable;
        $this->regionCollection = $regionCollection;
        $this->json = $json;
        $this->formKey = $formKey;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->sourceAttrRepository = $sourceAttrRepository;
        $this->logger = $logger;
    }

    /**
     * Get customer data
     *
     * @return Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomerData() {
        $session = $this->customerSession->create();
        if ($session->isLoggedIn()) {
            return $session->getCustomerData();
        }

        return false;
    }

    /**
     * is customer logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn() {
        $session = $this->customerSession->create();
        if ($session->isLoggedIn()) {
            return true;
        }

        return false;
    }

    /**
     * Get customer id
     *
     * @return int|bool
     */
    public function getLoggedInCustomerId() {
        if ($this->isCustomerLoggedIn()) {
            $session = $this->customerSession->create();
            return $session->getId();
        }

        return false;
    }

    /**
     * Create session data
     *
     * @return void
     */
    public function createReservationIdToSession($reserveId) {
        $session = $this->customerSession->create();
        if(!$session->getReservationId()) {
            $session->setReservationId($reserveId);
        }
    }

    /**
     * Get reservation id session
     *
     * @return string
     */
    public function getSessionReservationId()
    {
        $session = $this->customerSession->create();
        $this->logger->info('------------------ run ' . __FUNCTION__);
        $this->logger->info('session getReservationId() = ' . $session->getReservationId());
        $this->logger->info('------------------ end run ' . __FUNCTION__);
        return $session->getReservationId();
    }

    /**
     * Get Source Repository
     *
     * @return \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    public function getSourceRepository()
    {
        return $this->sourceRepository;
    }

    /**
     * Get Store Manager
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    /**
     * Get source
     *
     * @param string $code
     * @return \Magento\InventoryApi\Api\SourceInterface
     */
    public function getSourceByCode($code)
    {
        if($code) {
            return $this->sourceRepository->get($code);
        }
    }

    /**
     * retrive all sources
     *
     * @return Magento\Framework\Api\SearchCriteriaBuilder
     */
    public function getSources()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter('source_code', 'default', 'neq')->addFilter('enabled', 1, 'eq')->create();
        $sources = $this->sourceRepository->getList($searchCriteria)->getItems();

        return $sources;
    }

    /**
     * get source address
     *
     * @param \Magento\InventoryApi\Api\Data\SourceInterface
     * @return string
     */
    public function getSourceAddress($source)
    {
        $attr = $this->getSourceAttr($source);

        $data[] = isset($attr['street']) ? $attr['street'] : '';
        $data[] = isset($attr['region']) ? $attr['region'] : '';
        $data[] = isset($attr['city']) ? $attr['city'] : '';
        $data[] = isset($attr['district']) ? $attr['district'] : '';
        $data[] = isset($attr['postcode']) ? $attr['postcode'] : '';

        $string = implode(', ', $data);

        return $string;
    }

    /**
     * get source attribute
     *
     * @param \Magento\InventoryApi\Api\Data\SourceInterface
     * @return array
     */
    public function getSourceAttr($source)
    {
        $attrs = $this->sourceAttrRepository->getBySourceCode($source->getSourceCode());

        if($source->getRegionId()) {
            $region = $this->getRegionById($source->getRegionId());
        }

        $data['street'] = $source->getStreet() ? $source->getStreet() : '-';
        $data['region'] = $source->getRegionId() ? $region->getName() : '-';
        $data['city'] = $source->getCity() ? $source->getCity() : '-';
        // $data['district'] = $source->getDistrict() ? $source->getDistrict() : '-';
        $data['postcode'] = $source->getPostcode() ? $source->getPostcode() : '-';

        foreach($attrs as $attr) {
            $data[$attr->getAttribute()] = $attr->getValue();
        }

        return $data;
    }

    /**
     * get region data by id
     *
     * @param int $regId
     * @return \Magento\Directory\Api\Data\RegionInterface
     */
    public function getRegionById(int $regId)
    {
        $region = $this->regionCollection->create()
                        ->addFieldToFilter('main_table.region_id', ['eq' => $regId])
                        ->getFirstItem();

        return $region;
    }

    /**
     * retrive all product sources by sku
     *
     * @param string $sku
     * @return \Magento\InventoryApi\Api\Data\GetSourceItemsBySkuInterface
     */
    public function getProductSources(string $sku)
    {
        return $this->getSourceItem->execute($sku);;
    }

    /**
     * retrive all product sources filtered with buffer
     *
     * @param string $sku
     * @param int $buffer
     * @param string $condition
     * @return Magento\Framework\Api\SearchCriteriaBuilder
     */
    public function getFilteredProductSources(string $sku, int $buffer = 0, string $condition = 'greater_than')
    {
        switch ($condition) {
            case self::LESS_THAN:
                $cond = 'lt';
                break;

            default:
                $cond = 'gt';
                break;
        }

        $searchCriteriaBuilder = $this->searchCriteriaBuilder->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter('source_code', 'default', 'neq')
            ->addFilter('sku', $sku, 'eq')
            ->addFilter('quantity', $buffer, $cond)
            ->addFilter('status', 1, 'eq')
            ->addFilter('stock_id', 2, 'eq')
            ->create();
        $sources = $this->sourceItemRepository->getList($searchCriteria)->getItems();

        return $sources;
    }

    /**
     * change date format
     *
     * @param datetime $datetime
     * @return datetime
     */
    public function changeDateFormat($datetime, $format = 'd F Y H:i')
    {
        return $this->datetime->date($format, $datetime);
    }

    /**
     * get datetime
     *
     * @return \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * get timezone
     *
     * @return \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * get formkey
     *
     * @return \Magento\Framework\Data\Form\FormKey
     */
    public function getFormKey()
    {
        return $this->formKey;
    }

    /**
     * get logger
     *
     * @return Trans\Reservation\Logger\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * get json
     *
     * @return \Magento\Framework\Serialize\Serializer\Json
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getParentProductId($productId)
    {
        $parentConfigObject = $this->configurable->getParentIdsByChild($productId);

        if($parentConfigObject) {
            return $parentConfigObject[0];
        }

        return false;
    }

    /**
     * get Super Attribute details by configurable product id
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     */
    public function getSuperAttributeData(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        if ($product->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return [];
        }

        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productTypeInstance */
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter($product->getStoreId(), $product);

        $attributes = $productTypeInstance->getConfigurableAttributes($product);
        $superAttributeList = [];
        foreach($attributes as $_attribute){
            $attributeCode = $_attribute->getProductAttribute()->getAttributeCode();;
            $superAttributeList[$_attribute->getAttributeId()] = $attributeCode;
        }

        return $superAttributeList;
    }

    /**
     * Get min value of array
     *
     * @param array $data
     * @return array
     */
    public function getMinValueOfArray(array $data)
    {
        if(isset($data[0]['value'])) {
            $temp = $data[0]['value'];
            $min = $data[0];

            foreach($data as $row)
            {
                if($row['value'] < $temp)
                {
                    $temp = $row['value'];
                    $min = $row;
                }
            }

            return $min;
        }

        return false;
    }

    /**
     * Get max value of array
     *
     * @param array $data
     * @return array
     */
    public function getMaxValueOfArray(array $data)
    {
        if(isset($data[0]['value'])) {
            $temp = $data[0]['value'];
            $min = $data[0];

            foreach($data as $row)
            {
                if($row['value'] > $temp)
                {
                    $temp = $row['value'];
                    $min = $row;
                }
            }

            return $min;
        }

        return false;
    }

    /**
     * Get latest value of array
     *
     * @param array $data
     * @return array
     */
    public function getLatestOfArray(array $data)
    {
        if(isset($data[0]['created_at'])) {
            $temp = $data[0]['created_at'];
            $min = $data[0];

            foreach($data as $row)
            {
                if($row['created_at'] > $temp)
                {
                    $temp = $row['created_at'];
                    $min = $row;
                }
            }

            return $min;
        }

        return false;
    }
}
