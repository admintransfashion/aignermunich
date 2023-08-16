<?php
/**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_TCastSMS
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\TCastSMS\Ui\DataProvider\Grid;

use Magento\Ui\DataProvider\AbstractDataProvider;
use CTCD\TCastSMS\Model\ResourceModel\LogOtp\CollectionFactory;

class LogOtpDataProvider extends AbstractDataProvider
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
        $jsonFields = ['delivery_request_resp', 'delivery_status_resp'];
        $items = $this->collection->getItems();
        foreach ($items as $item) {
            if(! empty($dateFields)){
                foreach ($dateFields as $field) {
                    if(array_key_exists($field, $item->getData())){
                        $date = $item->getData($field);
                        $convertedDate = $this->ctcdCoreDateTimeHelper->convertUTCZeroToCurrentTimezone($date);
                        $item->setData($field, date('d-M-Y H:i:s', strtotime($convertedDate)));
                        unset($dateFields[$field]);
                    }
                }
            }
            if(! empty($jsonFields)){
                foreach ($jsonFields as $field) {
                    if(array_key_exists($field, $item->getData())){
                        $json = $item->getData($field);
                        $arrJson = json_decode($json);
                        if ($arrJson) {
                            $item->setData($field, json_encode($arrJson, JSON_PRETTY_PRINT));
                            if ($field === 'delivery_request_resp' && property_exists($arrJson, 'Data')) {
                                $data = (array) $arrJson->Data[0];
                                $responseString = '-';
                                if (isset($data['MessageErrorCode']) && isset($data['MessageErrorDescription'])) {
                                    $responseString = $data['MessageErrorDescription'];
                                    $responseString = (int) $data['MessageErrorCode'] === 0 ? $responseString : $responseString . ' ( Error Code: ' . $data['MessageErrorCode'] . ' )';
                                }
                                $item->setData('delivery_request_string', $responseString);
                            }
                            elseif ($field === 'delivery_status_resp' && property_exists($arrJson, 'Data')) {
                                $data = (array) $arrJson->Data;
                                $responseString = '-';
                                if (isset($data['Status']) && isset($data['ErrorCode'])) {
                                    $responseString = $data['Status'];
                                    $responseString = (int) $data['ErrorCode'] === 0 ? $responseString : $responseString . ' ( Error Code: ' . $data['ErrorCode'] . ' )';
                                }
                                $item->setData('delivery_status_string', $responseString);
                            }
                        }
                        unset($jsonFields[$field]);
                    }
                }
            }
            $this->loadedData[$item->getId()] = $item->getData();
        }

        return $this->loadedData;
    }
}
