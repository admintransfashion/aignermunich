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

namespace Trans\Reservation\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Trans\Reservation\Api\Data\ReservationConfigInterface;
use Trans\Reservation\Model\ResourceModel\ReservationConfig as ResourceModel;

/**
 * Class ReservationConfig
 *
 * @SuppressWarnings(PHPMD)
 */
class ReservationConfig extends \Magento\Framework\Model\AbstractModel implements ReservationConfigInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'trans_reservation_config';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_reservation_config';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_reservation_config';

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory
     */
    protected $configCollection;

    /**
     * @var \Trans\Reservation\Api\ReservationConfigRepositoryInterface
     */
    protected $configRepository;

    /**
     * @var \Trans\Reservation\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory $configCollection
     * @param \Trans\Reservation\Api\ReservationConfigRepositoryInterface $configRepository
     * @param \Trans\Reservation\Helper\Config $configHelper,
     * @param \Trans\Reservation\Helper\Data $dataHelper,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory $configCollection,
        \Trans\Reservation\Api\ReservationConfigRepositoryInterface $configRepository,
        \Trans\Reservation\Helper\Config $configHelper,
        \Trans\Reservation\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->json = $json;
        $this->productRepository = $productRepository;
        $this->configCollection = $configCollection;
        $this->configRepository = $configRepository;
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;

        $this->logger = $this->dataHelper->getLogger();

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(ReservationConfigInterface::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($dataId)
    {
        return $this->setData(ReservationConfigInterface::ID, $dataId);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->getData(ReservationConfigInterface::CONFIG);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig($config)
    {
        return $this->setData(ReservationConfigInterface::CONFIG, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(ReservationConfigInterface::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(ReservationConfigInterface::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryId()
    {
        return $this->getData(ReservationConfigInterface::CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(ReservationConfigInterface::CATEGORY_ID, $categoryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductSku()
    {
        return $this->getData(ReservationConfigInterface::PRODUCT_SKU);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductSku($productSku)
    {
        return $this->setData(ReservationConfigInterface::PRODUCT_SKU, $productSku);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData(ReservationConfigInterface::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(ReservationConfigInterface::VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter()
    {
        return $this->getData(ReservationConfigInterface::FILTER);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter($filter)
    {
        return $this->setData(ReservationConfigInterface::FILTER, $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(ReservationConfigInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created)
    {
        return $this->setData(ReservationConfigInterface::CREATED_AT, $created);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt() {
        return $this->getData(ReservationConfigInterface::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(ReservationConfigInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getFlag()
    {
        return $this->getData(ReservationConfigInterface::FLAG);
    }

    /**
     * {@inheritdoc}
     */
    public function setFlag($flag)
    {
        return $this->setData(ReservationConfigInterface::FLAG, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->getData(ReservationConfigInterface::STORE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStore($store)
    {
        return $this->setData(ReservationConfigInterface::STORE_CODE, $store);
    }

    /**
     * Get Reservation Config by Product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $config
     * @return array|bool
     */
    public function getConfigByProduct(\Magento\Catalog\Api\Data\ProductInterface $product, string $config)
    {
        $choosen = [];
        $sku = $product->getSku();

        try {
            $checkSku = $this->configRepository->getByProductSkus([$sku], $config);

            foreach($checkSku as $configData) {
                if($configData->getFlag()) {
                    $confProductSku = $this->json->unserialize($configData->getProductSku());

                    if(in_array($sku, $confProductSku)) {
                        $choosen[] = ['configId' => $configData->getId(), 'value' => $configData->getValue()];
                    }
                }
            }
        } catch (NoSuchEntityException $e) {
            $checkSku = false;
        } catch (\Exception $e) {
            $checkSku = false;
            $this->logger->info('Error get product buffer: ' . $e->getMessage());
        }

        if(!empty($choosen)) {
            return $choosen;
        }

        return false;
    }

    /**
     * Get Reservation Config by Category ids
     *
     * @param array $categoryIds
     * @param string $config
     * @return array|bool
     */
    public function getConfigByCategoryIds(array $categoryIds, string $config)
    {
        $choosen = [];

        foreach($categoryIds as $pcatId) {
            try {
                $collection = $this->configCollection->create();
                $collection->addFieldToFilter(ReservationConfigInterface::CONFIG, array('eq' => $config));
                $collection->addFieldToFilter(ReservationConfigInterface::FLAG, array('eq' => 1));
                $collection->addFieldToFilter(ReservationConfigInterface::CATEGORY_ID, array('like' => '%' . $pcatId . '%'));

                foreach($collection as $resConfig) {
                    $confCategoryId = $this->json->unserialize($resConfig->getCategoryId());

                    if(in_array($pcatId, $confCategoryId)) {
                        $choosen[] = ['configId' => $resConfig->getId(), 'value' => $resConfig->getValue()];
                    }
                }
            } catch (\Exception $e) {
                $this->logger->info('Error get product buffer: ' . $e->getMessage());
            }
        }

        if(!empty($choosen)) {
            return $choosen;
        }

        return false;
    }

    /**
     * get config by priority
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $config
     * @param string $sourceCode
     * @return array|bool
     */
    public function getConfigByPriority(\Magento\Catalog\Api\Data\ProductInterface $product, string $config, string $sourceCode)
    {
        $configPriority = $this->configHelper->getConfigPriority();
        $configPriority = $this->json->unserialize($configPriority);

        $configData = [];

        foreach($configPriority as $row) {
            if(!empty($configData)) {
                continue;
            }

            $collection = $this->configCollection->create();
            $collection->addFieldToFilter(ReservationConfigInterface::CONFIG, array('eq' => $config));
            $collection->addFieldToFilter(ReservationConfigInterface::FLAG, array('eq' => 1));

            switch ($row['filter']) {
                case ReservationConfigInterface::FILTER_PRODUCT:
                    $sku = $product->getSku();
                    try {
                        $collection->addFieldToFilter(ReservationConfigInterface::PRODUCT_SKU, array('like' => '%' . $sku . '%'));

                        foreach($collection as $resConfig) {
                            $confSku = $this->json->unserialize($resConfig->getProductSku());

                            if(in_array($sku, $confSku)) {
                                $store = $resConfig->getStore();
                                $inStore = true;
                                if($store) {
                                    $store = $this->json->unserialize($store);
                                    $inStore = $this->isStoreMatch($store, $sourceCode);
                                }

                                if($inStore) {
                                    $configData[] = ['configId' => $resConfig->getId(), 'value' => $resConfig->getValue(), 'created_at' => $resConfig->getUpdatedAt()];
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $this->logger->info('Error get product date: ' . $e->getMessage());
                    }
                    break;

                case ReservationConfigInterface::FILTER_CATEGORY:
                    $categoryIds = $product->getCategoryIds();

                    foreach($categoryIds as $pcatId) {
                        try {
                            $collection = $this->configCollection->create();
                            $collection->addFieldToFilter(ReservationConfigInterface::CONFIG, array('eq' => $config));
                            $collection->addFieldToFilter(ReservationConfigInterface::FLAG, array('eq' => 1));
                            $collection->addFieldToFilter(ReservationConfigInterface::CATEGORY_ID, array('like' => '%' . $pcatId . '%'));

                            foreach($collection as $resConfig) {
                                $confCategoryId = $this->json->unserialize($resConfig->getCategoryId());

                                if(in_array($pcatId, $confCategoryId)) {
                                    $store = $resConfig->getStore();
                                    $inStore = true;
                                    if($store) {
                                        $store = $this->json->unserialize($store);
                                        $inStore = $this->isStoreMatch($store, $sourceCode);
                                    }

                                    if($inStore) {
                                        $configData[] = ['configId' => $resConfig->getId(), 'value' => $resConfig->getValue(), 'created_at' => $resConfig->getUpdatedAt()];
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            $this->logger->info('Error get product date: ' . $e->getMessage());
                        }
                    }

                    break;

                case ReservationConfigInterface::FILTER_STORE:

                    try {
                        $collection->addFieldToFilter(ReservationConfigInterface::FILTER, array('eq' => ReservationConfigInterface::FILTER_STORE));
                        $collection->addFieldToFilter(ReservationConfigInterface::STORE_CODE, array('like' => '%' . $sourceCode . '%'));
                        // echo $collection->getSelect();
                        // $collection->addFieldToFilter(
                        //     array(ReservationConfigInterface::STORE_CODE, ReservationConfigInterface::STORE_CODE),
                        //     array(
                        //         array('like' => '%' . $sourceCode . '%'),
                        //         array('null' => true)
                        //     )
                        // );

                        foreach($collection as $resConfig) {
                            $confStore = $this->json->unserialize($resConfig->getStore());
                            $match = true;

                            if($match) {
                                $configData[] = ['configId' => $resConfig->getId(), 'value' => $resConfig->getValue(), 'created_at' => $resConfig->getUpdatedAt()];
                            }
                        }
                    } catch (\Exception $e) {
                        $this->logger->info('Error get product date: ' . $e->getMessage());
                    }

                    break;
            }
        }
        return $configData;
    }

    /**
     * is Store match
     *
     * @param array $store
     * @param string sourceCode
     * @return bool
     */
    protected function isStoreMatch(array $store, string $sourceCode) {
        $match = true;

        if(is_array($store)) {
            if(!in_array($sourceCode, $store)) {
                $match = false;
            }
        } else {
            if($store != $sourceCode) {
                $match = false;
            }
        }

        return $match;
    }

    /**
     * get product buffer config
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $sourceCode
     * @return int
     */
    public function getProductBuffer(\Magento\Catalog\Api\Data\ProductInterface $product, string $sourceCode)
    {
        $this->logger->info('=================== Start Get Product Buffer ===================');
        $choosenBuffer = [];

        $buffer = $this->configHelper->getGlobalProductBuffer();
        $config = ReservationConfigInterface::CONFIG_BUFFER;
        $sku = $product->getSku();
        $prodCategoryIds = $product->getCategoryIds();

        $filterPriority = $this->configHelper->getFilterPriority();
        $this->logger->info('run ' . __FUNCTION__ . ' $filterPriority = ' . $this->json->serialize($filterPriority));
        $bufferPriority = $this->configHelper->getBufferPriority();
        $this->logger->info('run ' . __FUNCTION__ . ' $bufferPriority = ' . $this->json->serialize($bufferPriority));

        try {
            $choosenBuffer = $this->getConfigByPriority($product, $config, $sourceCode);

            if(!empty($choosenBuffer)) {
                $this->logger->info('run ' . __FUNCTION__ . ' $sourceCode = ' . $sourceCode);
                $this->logger->info('run ' . __FUNCTION__ . ' $choosenBuffer = ' . $this->json->serialize($choosenBuffer));
                if($bufferPriority == ReservationConfigInterface::CONFIG_PRIORITY_MOST) {
                    $buffer = max(array_column($choosenBuffer, 'value'));
                }

                if($bufferPriority == ReservationConfigInterface::CONFIG_PRIORITY_FEWEST) {
                    $buffer = min(array_column($choosenBuffer, 'value'));
                }

                if($bufferPriority == ReservationConfigInterface::CONFIG_PRIORITY_LATEST) {
                    $latest = $this->dataHelper->getLatestOfArray($choosenBuffer);
                    if($latest) {
                        $buffer = $latest['value'];
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('run ' . __FUNCTION__ . ' error = ' . $e->getMessage());
        }

        $this->logger->info('=================== End Get Product Buffer ===================');

        return (int)$buffer;
    }

    /**
     * get product max qty
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $sourceCode
     * @return int
     */
    public function getMaxQty(\Magento\Catalog\Api\Data\ProductInterface $product, string $sourceCode)
    {
        $this->logger->info('=================== Start Get Product Max Qty Config ===================');
        $this->logger->info('run ' . __FUNCTION__);

        $choosen = [];

        $maxQty = $this;
        $config = ReservationConfigInterface::CONFIG_MAXQTY;
        $sku = $product->getSku();
        $prodCategoryIds = $product->getCategoryIds();

        $filterPriority = $this->configHelper->getFilterPriority();
        $this->logger->info('run ' . __FUNCTION__ . ' $filterPriority = ' . $filterPriority);
        $configPriority = $this->configHelper->getMaxqtyPriority();

        try {
            $choosen = $this->getConfigByPriority($product, $config, $sourceCode);

            if(!empty($choosen)) {
                $this->logger->info('run ' . __FUNCTION__ . ' $choosen = ' . $this->json->serialize($choosen));
                $this->logger->info('run ' . __FUNCTION__ . ' $sourceCode = ' . $sourceCode);
                $this->logger->info('run ' . __FUNCTION__ . ' $configPriority = ' . $configPriority);

                if($configPriority == ReservationConfigInterface::CONFIG_PRIORITY_MOST) {
                    $dataArray = $this->dataHelper->getMaxValueOfArray($choosen);
                }

                if($configPriority == ReservationConfigInterface::CONFIG_PRIORITY_FEWEST) {
                    $dataArray = $this->dataHelper->getMinValueOfArray($choosen);
                }

                if($configPriority == ReservationConfigInterface::CONFIG_PRIORITY_LATEST) {
                    $dataArray = $this->dataHelper->getLatestOfArray($choosen);
                }

                try {
                    $maxQty = $this->configRepository->getById($dataArray['configId']);
                    return $maxQty;
                } catch (NoSuchEntityException $e) {
                    $this->logger->info('No max qty config for this product');
                    throw new NoSuchEntityException(__("No max qty config for this product"));
                }
            } else {
                $this->logger->info('No max qty config for this product');
                throw new NoSuchEntityException(__("No max qty config for this product"));
            }
        } catch (\Exception $e) {
            $this->logger->info('run ' . __FUNCTION__ . ' error = ' . $e->getMessage());
            throw new NoSuchEntityException(__("No max qty config for this product"));
        }

        $this->logger->info('=================== End Get Product Max Qty Config ===================');
    }

    /**
     * get product reservation hours
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return array
     */
    public function getProductReservationHours(\Magento\Catalog\Api\Data\ProductInterface $product, string $sourceCode)
    {
        $this->logger->info('=================== Start Get Product Reservation Hours ===================');
        $choosen = [];

        $hours = false;
        $config = ReservationConfigInterface::CONFIG_HOURS;
        $sku = $product->getSku();
        $prodCategoryIds = $product->getCategoryIds();

        $filterPriority = $this->configHelper->getFilterPriority();
        $hoursPriority = $this->configHelper->getHoursPriority();
        $this->logger->info('run ' . __FUNCTION__ . ' $hoursPriority = ' . $this->json->serialize($hoursPriority));

        try {
            $choosen = $this->getConfigByPriority($product, $config, $sourceCode);

            if(!empty($choosen)) {
                $this->logger->info('run ' . __FUNCTION__ . ' $choosen = ' . $this->json->serialize($choosen));
                if($hoursPriority == ReservationConfigInterface::CONFIG_PRIORITY_LONGEST) {
                    $hours = $this->dataHelper->getMaxValueOfArray($choosen);
                }

                if($hoursPriority == ReservationConfigInterface::CONFIG_PRIORITY_SHORTEST) {
                    $hours = $this->dataHelper->getMinValueOfArray($choosen);
                }

                if($hoursPriority == ReservationConfigInterface::CONFIG_PRIORITY_LATEST) {
                    $hours = $this->dataHelper->getLatestOfArray($choosen);
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('run ' . __FUNCTION__ . ' error = ' . $e->getMessage());
        }

        $this->logger->info('=================== Start Get Product Reservation Hours ===================');

        return $hours;
    }

    /**
     * get reservation date config
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $sourceCode
     * @return array
     */
    public function getDateConfig(\Magento\Catalog\Api\Data\ProductInterface $product, string $sourceCode)
    {
        $this->logger->info('=================== Start Get Reservation Date Config ===================');
        $choosen = [];

        $date = false;
        $config = ReservationConfigInterface::CONFIG_DATE;
        $sku = $product->getSku();
        $prodCategoryIds = $product->getCategoryIds();

        $filterPriority = $this->configHelper->getFilterPriority();
        $datePriority = $this->configHelper->getDatePriority();
        $this->logger->info('run ' . __FUNCTION__ . ' $datePriority = ' . $this->json->serialize($datePriority));
        $this->logger->info('run ' . __FUNCTION__ . ' $sourceCode = ' . $sourceCode);
        $this->logger->info('run ' . __FUNCTION__ . ' $sku = ' . $product->getSku());

        // try {
            $choosen = $this->getConfigByPriority($product, $config, $sourceCode);

            if(!empty($choosen)) {
                $this->logger->info('run ' . __FUNCTION__ . ' $choosen = ' . $this->json->serialize($choosen));
                if($datePriority == ReservationConfigInterface::CONFIG_PRIORITY_LONGEST) {
                    $date = $this->dataHelper->getMaxValueOfArray($choosen);
                }

                if($datePriority == ReservationConfigInterface::CONFIG_PRIORITY_SHORTEST) {
                    $date = $this->dataHelper->getMinValueOfArray($choosen);
                }

                if($datePriority == ReservationConfigInterface::CONFIG_PRIORITY_LATEST) {
                    $date = $this->dataHelper->getLatestOfArray($choosen);
                }
            }
        // } catch (\Exception $e) {
        //     $this->logger->info('run ' . __FUNCTION__ . ' error = ' . $e->getMessage());
        // }

        $this->logger->info('=================== Start Get Reservation Date Config ===================');

        return $date;
    }

    /**
     * is product assigned to config data
     *
     * @param \Tran\Reservation\Api\Data\ReservationConfigInterface $config
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return bool
     */
    public function isProductAssignedToConfig(\Trans\Reservation\Api\Data\ReservationConfigInterface $config, \Magento\Catalog\Api\Data\ProductInterface $product, string $sourceCode)
    {
        $check = $this->checkProductConfig($config, $product, $sourceCode);

        if($check == false) {
            $parentId = $this->dataHelper->getParentProductId($product->getId());
            if($parentId) {
                $product = $this->productRepository->getById($parentId);
                $check = $this->checkProductConfig($config, $product);
            }
        }

        return $check;
    }

    /**
     * is product assigned to config data
     *
     * @param \Tran\Reservation\Api\Data\ReservationConfigInterface $config
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $sourceCode
     * @return bool
     */
    public function checkProductConfig(\Trans\Reservation\Api\Data\ReservationConfigInterface $config, \Magento\Catalog\Api\Data\ProductInterface $product, string $sourceCode)
    {
        $sku = $product->getSku();
        $this->logger->info('run ' . __FUNCTION__ . ' $sku = ' . $sku);
        $this->logger->info('run ' . __FUNCTION__ . ' $sourceCode = ' . $sourceCode);

        /** Check by sku **/
        if(!empty($config->getProductSku())) {
            $this->logger->info('run ' . __FUNCTION__ . ' $config->getProductSku() = ' . $config->getProductSku());
            $configSkus = $this->json->unserialize($config->getProductSku());
            if(in_array($sku, $configSkus)) {
                return true;
            }
        }

        /** Check by category **/
        if(!empty($config->getCategoryId())) {
            $prodCategoryIds = $product->getCategoryIds();
            $this->logger->info('run ' . __FUNCTION__ . ' $config->getCategoryId() = ' . $config->getCategoryId());
            $this->logger->info('run ' . __FUNCTION__ . ' $prodCategoryIds = ' . $this->json->unserialize($prodCategoryIds));
            $configCategory = $this->json->unserialize($config->getCategoryId());
            $check = array_intersect($configCategory, $prodCategoryIds);

            if(count($check)) {
                return true;
            }
        }

        /** Check by store **/
        if(!empty($config->getStore())) {
            $this->logger->info('run ' . __FUNCTION__ . ' $config->getStore() = ' . $config->getStore());
            $store = $this->json->unserialize($config->getStore());

            if(in_array($sourceCode, $store)) {
                return true;
            }
        }

        return false;
    }
}
