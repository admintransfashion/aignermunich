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
 * @author   imam kusuma <imam.kusuma@ctcorpdigital.com>
 */

namespace CTCD\Core\Helper;

class Json extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerialize;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $serialize;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerialize
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerialize,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize
    ) {
        parent::__construct($context);

        $this->jsonSerialize = $jsonSerialize;
        $this->serialize = $serialize;
    }

    /**
     * Check the string is a valid JSON string
     *
     * @param string $string
     * @return boolean
     */
    public function isValidJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /**
     * Check the string is a valid serialize string
     *
     * @param string $string
     * @return boolean
     */
    public function isValidSerializeString($string)
    {
        $valid = true;
        if(is_string($string)) {
            $array = @unserialize($string);
            if ($array === false && $string !== 'b:0;') {
                $valid = false;
            }
        }
        return $valid;
    }

    /**
     * Create php serialize
     *
     * @param array $data
     * @return string | null
     */
    public function serialize($data = []) {
        $result = '';
        if (!empty($data)) {
            $result = $this->serialize->serialize($data);
        }

        return $result;
    }

    /**
     * Create serialize
     *
     * @param array $data
     * @return string | null
     */
    public function serializeJson($data = []) {
        $result = '{}';
        if (!empty($data)) {
            $result = $this->jsonSerialize->serialize($data);
        }
        return $result;
    }

    /**
     * decode json serialize
     *
     * @param string $data
     * @return array
     */
    public function unserializeJson($data = '') {
        $result = [];
        if ($data && $this->isValidJson($data)) {
            $result = $this->jsonSerialize->unserialize($data);
        }
        return $result;
    }

    /**
     * unserialize
     *
     * @param string $data
     * @return array
     */
    public function unserialize($data = '') {
        $result = [];
        if ($data && $this->isValidSerializeString($data)) {
            $result = $this->serialize->unserialize($data);
        }
        return $result;
    }
}
