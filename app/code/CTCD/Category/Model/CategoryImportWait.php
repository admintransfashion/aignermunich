<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Model;

use CTCD\Category\Api\Data\CategoryImportWaitInterface;
use CTCD\Category\Model\ResourceModel\CategoryImportWait as ResourceModel;

/**
 * Class CategoryImportWait
 */
class CategoryImportWait extends \Magento\Framework\Model\AbstractModel implements CategoryImportWaitInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'CTCD_CategoryImportWait';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'CTCD_CategoryImportWait';

    /**
     * @return void
     *
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

    /**
     * {@inheritdoc}
     */
    public function getJsonData()
    {
        return $this->getData(CategoryImportWaitInterface::JSON_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setJsonData($data)
    {
        $this->setData(CategoryImportWaitInterface::JSON_DATA, $data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonKeys()
    {
        return $this->getData(CategoryImportWaitInterface::JSON_KEYS);
    }

    /**
     * {@inheritdoc}
     */
    public function setJsonKeys($keys)
    {
        $this->setData(CategoryImportWaitInterface::JSON_KEYS, $keys);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(CategoryImportWaitInterface::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->setData(CategoryImportWaitInterface::CODE, $code);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created)
    {
        $this->setData(CategoryImportWaitInterface::CREATED_AT, $created);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updated)
    {
        $this->setData(CategoryImportWaitInterface::UPDATED_AT, $updated);
        return $this;
    }
}
