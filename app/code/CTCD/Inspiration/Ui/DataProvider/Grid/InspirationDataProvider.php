<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Ui\DataProvider\Grid;

use Magento\Ui\DataProvider\AbstractDataProvider;
use CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory;

class InspirationDataProvider extends AbstractDataProvider
{

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \CTCD\Core\Helper\DateTime
     */
    protected $ctcdCoreDateTimeHelper;

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
        \CTCD\Core\Helper\DateTime $ctcdCoreDateTimeHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->ctcdCoreDateTimeHelper = $ctcdCoreDateTimeHelper;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $dateFields = ['created_at','updated_at'];
        $items = $this->collection->getItems();
        foreach ($items as $item) {
            if(! empty($dateFields)){
                foreach ($dateFields as $field) {
                    if(array_key_exists($field, $item->getData())){
                        $date = $item->getData($field);
                        $item->setData($field, $this->ctcdCoreDateTimeHelper->convertUTCZeroToCurrentTimezone($date));
                        unset($dateFields[$field]);
                    }
                }
            }
            $this->loadedData[$item->getId()] = $item->getData();
        }

        return $this->loadedData;
    }
}
