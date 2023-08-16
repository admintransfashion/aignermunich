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

namespace Trans\Reservation\Ui\DataProvider;

use Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory;
use Trans\Reservation\Api\Data\ReservationConfigInterface;

/**
 * Class ReservationConfigProvider
 */
class ReservationConfigProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Serialize\Serializer\Json $json,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->json = $json;
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }

        $items = $this->collection->getItems();

        foreach ($items as $item) {
            $data = $this->prepareData($item->getData());

            $this->_loadedData[$item->getId()] = $data;
        }

        return $this->_loadedData;
    }

    /**
     * prepare config data
     *
     * @param array $config
     * @return array
     */
    protected function prepareData(array $config)
    {
        $productSkus = $config['product_skus'];
        $categoryIds = $config['category_ids'];
        $storeCode = $config['store_code'];
        $filter = $config['filter'];

        if($filter == ReservationConfigInterface::FILTER_PRODUCT) {
            unset($config['data']['category_ids']);
            $categoryIds = null;
        }

        if($filter == ReservationConfigInterface::FILTER_CATEGORY) {
            unset($config['product_skus']);
            $productSkus = null;
        }

        if($filter == ReservationConfigInterface::FILTER_STORE) {
            unset($config['data']['category_ids']);
            unset($config['product_skus']);
            $productSkus = null;
            $categoryIds = null;
        }

        if(!empty($productSkus) || $productSkus != null) {
            $dinamycRow = array();
            foreach($this->json->unserialize($productSkus) as $key => $sku) {
                $data['record_id'] = $key;
                $data['sku'] = $sku;
                array_push($dinamycRow, $data);
            }
            $config['product_skus'] = $dinamycRow;
        }

        if(!empty($categoryIds)) {
            $config['data']['category_ids'] = $this->json->unserialize($categoryIds);
        }

        if(!empty($storeCode)) {
            $config['store_code'] = $this->json->unserialize($storeCode);
        }

        return $config;
    }
}
