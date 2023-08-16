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

use CTCD\Category\Api\Data\CategoryImportInterface;
use CTCD\Category\Model\ResourceModel\CategoryImport as ResourceModel;

/**
 * Class CategoryImport
 */
class CategoryImport extends \Magento\Framework\Model\AbstractModel implements CategoryImportInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'CTCD_CategoryImport';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'CTCD_CategoryImport';

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
    public function setFile($file)
    {
        $this->setData(CategoryImportInterface::FILE, $file);
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->getData(CategoryImportInterface::FILE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->setData(CategoryImportInterface::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(CategoryImportInterface::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminId($adminId)
    {
        $this->setData(CategoryImportInterface::ADMIN_ID, $adminId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminId()
    {
        return $this->getData(CategoryImportInterface::ADMIN_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created)
    {
        $this->setData(CategoryImportInterface::CREATED_AT, $created);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updated)
    {
        $this->setData(CategoryImportInterface::UPDATED_AT, $updated);
    }
}
