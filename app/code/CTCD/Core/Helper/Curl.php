<?php
/**
 * @category CTCD
 * @package  CTCD_Core
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace CTCD\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Zend\Http\Client;

class Curl extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var  Context
     */
    protected $context;

    /**
     * @var Client
     */
    protected $zendClient;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateTimeFactory;

    /**
     * @param Zend\Http\Client $zenClient
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param Magento\Framework\App\Helper\Context $context
     * @param Trans\Integration\Helper\Config $config
     */
    public function __construct(
        Context $context,
        Client $zendClient,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory
    ) {
        parent::__construct($context);
        $this->zendClient = $zendClient;
        $this->curl = $curl;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * Curl POST
     *
     * @param string $url
     * @param array $headers
     * @param array|string $params
     */
    public function curlPost($url = "", $headers = [], $params = "")
    {
        try {
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_TIMEOUT, 30);
            $this->curl->setOption(CURLOPT_SSL_VERIFYHOST, false);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $this->curl->setHeaders($headers);
            $this->curl->post($url, $params);

            return $response = $this->curl->getBody();
        } catch (\Zend\Http\Exception\RuntimeException $runtimeException) {
            throw new StateException(__(
                $runtimeException->getMessage()
            ));
        }
    }

    /**
     * Curl GET
     *
     * @param string $url
     * @param array $headers
     */
    public function curlGet($url = "", $headers = [])
    {
        try {
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_TIMEOUT, 30);
            $this->curl->setOption(CURLOPT_SSL_VERIFYHOST, false);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
            $this->curl->setHeaders($headers);
            $this->curl->get($url);

            return $response = $this->curl->getBody();
        } catch (\Zend\Http\Exception\RuntimeException $runtimeException) {
            throw new StateException(__(
                $runtimeException->getMessage()
            ));
        }
    }

    /**
     * @return mixed|string
     * @throws StateException
     */
    public function call()
    {
        try {
            $this->zendClient->send();
            $response = $this->zendClient->getResponse();
            return $response->getBody();
        } catch (\Zend\Http\Exception\RuntimeException $runtimeException) {
            throw new StateException(__(
                $runtimeException->getMessage()
            ));
        }
    }

    /**
     * CURL send with custom response
     *
     * @return array
     */
    public function send()
    {
        $response = ['code' => null, 'body' => null];
        try {
            $this->zendClient->send();
            $curlResponse = $this->zendClient->getResponse();
            $response = ['code' => $curlResponse->getStatusCode(), 'body' => $curlResponse->getBody()];
        } catch (\Zend\Http\Exception\RuntimeException $e) {
        }
        return $response;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param array|string $queryParam
     * @param bool $rawBody
     * @throws StateException
     */
    public function prepare($method, $url = "", $headers = [], $queryParam = [], $rawBody = false, $fileUpload = false)
    {
        try {
            $this->zendClient->reset();
            $this->zendClient->setUri($url);

            switch (strtolower($method)) {
                case 'post':
                    $method = \Zend\Http\Request::METHOD_POST;
                    break;

                case 'put':
                    $method = \Zend\Http\Request::METHOD_PUT;
                    break;

                case 'delete':
                    $method = \Zend\Http\Request::METHOD_DELETE;
                    break;

                default:
                    $method = \Zend\Http\Request::METHOD_GET;
                    break;
            }

            $this->zendClient->setMethod($method);

            if (!empty($headers)) {
                // $headers = $this->setParams($headers);
                $this->zendClient->setHeaders($headers);
            }

            $options = ['maxredirects' => 0, 'timeout' => 30, 'sslverifypeer' => false];
            if (!empty($options)) {
                $this->zendClient->setOptions($options);
            }

            if (!empty($queryParam)) {
                if(!$fileUpload) {
                    switch ($rawBody) {
                        case true:
                            $this->zendClient->setRawBody($queryParam);
                            break;

                        default:
                            if($method == \Zend\Http\Request::METHOD_POST) {
                                $this->zendClient->setParameterPost($queryParam);
                            }

                            if($method == \Zend\Http\Request::METHOD_GET) {
                                $this->zendClient->setParameterGet($queryParam);
                            }
                            break;
                    }
                }
            }
        } catch (\Zend\Http\Exception\InvalidArgumentException $argumentException) {
            throw new StateException(__(
                $argumentException->getMessage()
            ));
        }

        return $this;
    }

    /**
     * @param $param
     * @return mixed|string
     */
    public function jsonToArray($param)
    {

        try {
            if (empty($param)) {
                return [];
            }
            $result = json_decode($param, true);
        } catch (\Exception $ex) {
            throw new StateException(
                __($ex->getMessage())
            );
        }
        return $result;
    }

    /**
     * @param array $param
     * @throws StateException
     */
    private function setParams($param = array())
    {
        if (!is_array($param)) {
            return $param;
        }

        $param = array_filter($param);
        return $this->generateJsonToArray($param);
    }

    /**
     * Different Output array with number / no string key.
     * @param $param
     * @return array
     */
    private function generateJsonToArray($param)
    {
        $query = [];
        $i = 0;
        foreach ($param as $key => $row) {
            $query[] = $key . ':' . $row;
            $i++;
        }
        return $query;
    }
}
