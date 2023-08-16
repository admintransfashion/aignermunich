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

/**
 * Class ProductBuffer
 */
class ProductBuffer extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
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

        foreach ($items as $buffer) {
            $data = $this->prepareBuffer($buffer->getData());

            $this->_loadedData[$buffer->getId()] = $data;
        }

        return $this->_loadedData;
    }

    /**
     * prepare buffer data
     *
     * @param array $buffer
     * @return array
     */
    protected function prepareBuffer(array $buffer)
    {
        $productSkus = $buffer['product_skus'];
        $categoryIds = $buffer['category_ids'];

        if(!empty($productSkus)) {
            $dinamycRow = array();
            foreach(unserialize($productSkus) as $key => $sku) {
                $data['record_id'] = $key;
                $data['sku'] = $sku;
                array_push($dinamycRow, $data);
            }
            $buffer['product_skus'] = $dinamycRow;
        } else {
            unset($buffer['product_skus']);
        }

        if(!empty($categoryIds)) {
            $buffer['data']['category_ids'] = unserialize($categoryIds);
        } else {
            unset($buffer['data']['category_ids']);
        }

        return $buffer;
    }
}
