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

use CTCD\Category\Api\Data\CategoryImportMapInterface;
use CTCD\Category\Model\ResourceModel\CategoryImportMap as ResourceModel;

/**
 * Class CategoryImportMap
 */
class CategoryImportMap extends \Magento\Framework\Model\AbstractModel implements CategoryImportMapInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'CTCD_CategoryImportMap';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'CTCD_CategoryImportMap';

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
    public function getCategoryCode()
    {
        return $this->getData(CategoryImportMapInterface::CATEGORY_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryCode($code)
    {
        $this->setData(CategoryImportMapInterface::CATEGORY_CODE, $code);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryId()
    {
        return $this->getData(CategoryImportMapInterface::CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryId($categoryId)
    {
        $this->setData(CategoryImportMapInterface::CATEGORY_ID, $categoryId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOfflineId()
    {
        return $this->getData(CategoryImportMapInterface::OFFLINE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOfflineId($offlineId)
    {
        $this->setData(CategoryImportMapInterface::OFFLINE_ID, $offlineId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created)
    {
        $this->setData(CategoryImportMapInterface::CREATED_AT, $created);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updated)
    {
        $this->setData(CategoryImportMapInterface::UPDATED_AT, $updated);
        return $this;
    }
}
