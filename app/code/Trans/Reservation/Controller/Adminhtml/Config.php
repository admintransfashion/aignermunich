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

namespace Trans\Reservation\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Trans\Reservation\Api\Data\ReservationConfigInterface;

/**
 * Class config
 */
abstract class Config extends \Magento\Backend\App\Action
{
    /**
     * Constant for product config title
     */
    const DEFAULT_TITLE = 'Product buffer';

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Trans_Reservation::reservation';

    /**
     * @var \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Trans\Reservation\Model\ReservationConfigFactory
     */
    protected $configFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Trans\Reservation\Api\ReservationConfigRepositoryInterface
     */
    protected $configRepository;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Context $context
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory $collectionFactory
     * @param \Trans\Reservation\Model\ReservationConfigFactory $configFactory
     * @param \Trans\Reservation\Api\ReservationConfigRepositoryInterface $configRepository
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Helper\Reservation $reservationHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory $collectionFactory,
        \Trans\Reservation\Model\ReservationConfigFactory $configFactory,
        \Trans\Reservation\Api\ReservationConfigRepositoryInterface $configRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Reservation $reservationHelper
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->collectionFactory = $collectionFactory;
        $this->configFactory = $configFactory;
        $this->configRepository = $configRepository;
        $this->dataHelper = $dataHelper;
        $this->reservationHelper = $reservationHelper;
        $this->logger = $this->dataHelper->getLogger();
        $this->json = $this->dataHelper->getJson();

        parent::__construct($context);
    }

    /**
     * init config
     *
     * @return \Trans\Reservation\Api\Data\ReservationConfigInterface
     */
    protected function initConfig()
    {
        $configid = $this->getRequest()->getParam('id');

        try {
            $config = $this->configRepository->getById($configid);
        } catch (NoSuchEntityException $e) {
            $config = $this->configFactory->create();
        }

        return $config;
    }

    /**
     * Generate product config title
     *
     * @return string
     */
    protected function generateTitle($config, $qty = 0)
    {
        return $this->reservationHelper->generateTitle($config, $qty);
    }

    /**
     * Generate product sku
     *
     * @param array $productSkus
     * @return string
     */
    protected function generateProductSkus(array $productSkus)
    {
        if(!empty($productSkus)) {
            $data = [];
            foreach($productSkus as $row) {
                $sku = $row['sku'];
                $data[] = $sku;
            }

            $data = $this->json->serialize($data);

            return $data;
        }
    }

    /**
     * Validate category ids
     *
     * @param array $categoryIds
     * @param string $config
     * @return array
     */
    protected function validateCategory(array $categoryIds, string $config, $bulk = false, $value = null)
    {
        $result['result'] = true;
        $exists = [];
        $configid = $this->getRequest()->getParam('id');

        foreach($categoryIds as $categoryId) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('config', ['eq' => $config]);

            if($configid) {
                $collection->addFieldToFilter('id', ['neq' => $configid]);
            }

            if($bulk && $value != null) {
                $collection->addFieldToFilter('value', ['neq' => $value]);
            }

            $collection->getSelect()->where('category_ids LIKE "%?%"', (int)$categoryId);

            foreach($collection as $row) {
                $current = $this->json->unserialize($row->getCategoryId());
                if(in_array($categoryId, $current)) {
                    try {
                        $category = $this->categoryRepository->get($categoryId);
                        $exists[] = $category->getName();
                    } catch (NoSuchEntityException $e) {
                        $exists[] = $categoryId;
                    }
                }
            }
        }

        // $result['result'] = count($exists) ? false : true;

        // if(!$result['result']) {
        //     $exists = array_unique($exists);
        //     $message = implode(', ', $exists);
        //     $result['message'] = __('Category is unique. Category %1 already assigned.', $message);
        // }

        return $result;
    }

    /**
     * Validate product skus
     *
     * @param json $productSkus
     * @param string $config
     * @return array
     */
    protected function validateProduct(string $productSkus, string $config, $bulk = false, $value = null)
    {
        $result['result'] = true;
        $exists = [];
        $configid = $this->getRequest()->getParam('id');

        foreach($this->json->unserialize($productSkus) as $sku) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('config', ['eq' => $config]);

            if($configid) {
                $collection->addFieldToFilter('id', ['neq' => $configid]);
            }

            if($bulk && $value != null) {
                $collection->addFieldToFilter('value', ['neq' => $value]);
            }

            $collection->getSelect()->where('product_skus LIKE "%' . $sku . '%"');

            foreach($collection as $row) {
                $current = $this->json->unserialize($row->getProductSku());
                if(in_array($sku, $current)) {
                    $exists[] = $sku;
                }
            }
        }

        // $result['result'] = count($exists) ? false : true;

        // if(!$result['result']) {
        //     $exists = array_unique($exists);
        //     $message = implode(', ', $exists);
        //     $result['message'] = __('Product sku is unique. Product with sku(s) %1 already assigned.', $message);
        // }

        return $result;
    }

    /**
     * Validate config value
     *
     * @param int $value
     * @param string $config
     * @return array
     */
    protected function validateValue(int $value, string $config, $bulk = false)
    {
        $result['result'] = true;
        $configid = $this->getRequest()->getParam('id');

        $label = $this->getConfigLabel($config);

        if($bulk == true) {
            $label = 'Value';
        }

        if($value == 0) {
            $result['result'] = false;
            $result['message'] = __('Field %1 must be greater than 0.', $label);
            return $result;
        }

        if($bulk == false) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('config', ['eq' => $config]);
            if($configid) {
                $collection->addFieldToFilter('id', ['neq' => $configid]);
            }
            $collection->addFieldToFilter('value', ['eq' => $value]);

            $result['result'] = $collection->getSize() ? false : true;

            if(!$result['result']) {
                $result['message'] = __('Field %1 is unique. Config with %1 %2 already exists.', $label, $value);
            }
        }

        return $result;
    }

    /**
     * get config field label
     *
     * @param string $config
     * @return string
     */
    protected function getConfigLabel(string $config)
    {
        switch ($config) {
            case ReservationConfigInterface::CONFIG_BUFFER:
                $label = ReservationConfigInterface::CONFIG_BUFFER_LABEL;
                break;

            case ReservationConfigInterface::CONFIG_MAXQTY:
                $label = ReservationConfigInterface::CONFIG_MAXQTY_LABEL;
                break;

            case ReservationConfigInterface::CONFIG_HOURS:
                $label = ReservationConfigInterface::CONFIG_HOURS_LABEL;
                break;

            default:
                $label = $config;
                break;
        }

        return $label;
    }
}
