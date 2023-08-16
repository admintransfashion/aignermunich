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

use CTCD\Category\Api\Data\CategoryImportDataInterface;
use CTCD\Category\Model\ResourceModel\CategoryImportData as ResourceModel;

/**
 * Class CategoryImportData
 */
class CategoryImportData extends \Magento\Framework\Model\AbstractModel implements CategoryImportDataInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'CTCD_CategoryImportData';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'CTCD_CategoryImportData';

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
     * @return array
     */
    public function getKeys()
    {
        $keys = [];

        if($this->getColumn()) {
            $columns = json_decode($this->getColumn());

            if(is_array($columns)) {
                foreach ($columns as $index => $col) {
                    $keys[$col] = $index;
                }
            }
        }

        return $keys;
    }

    /**
     * @return array
     */
    public function getDataToArray()
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getImportId()
    {
        return $this->getData(CategoryImportDataInterface::IMPORT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setImportId($importId)
    {
        $this->setData(CategoryImportDataInterface::IMPORT_ID, $importId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonData()
    {
        return $this->getData(CategoryImportDataInterface::JSON_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setJsonData($data)
    {
        $this->setData(CategoryImportDataInterface::JSON_DATA, $data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSequence()
    {
        return $this->getData(CategoryImportDataInterface::SEQUENCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSequence($seq)
    {
        $this->setData(CategoryImportDataInterface::SEQUENCE, $seq);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(CategoryImportDataInterface::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->setData(CategoryImportDataInterface::STATUS, $status);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn()
    {
        return $this->getData(CategoryImportDataInterface::COLUMN);
    }

    /**
     * {@inheritdoc}
     */
    public function setColumn($column)
    {
        $this->setData(CategoryImportDataInterface::COLUMN, $column);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created)
    {
        $this->setData(CategoryImportDataInterface::CREATED_AT, $created);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updated)
    {
        $this->setData(CategoryImportDataInterface::UPDATED_AT, $updated);
        return $this;
    }
}
