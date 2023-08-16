<?php
/**
 * @category Trans
 * @package  Trans_CatalogMultisource
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CatalogMultisource\Model\Source;

use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface;
use Trans\CatalogMultisource\Model\ResourceModel\SourceItemUpdateHistory as ResourceModel;

/**
 * Class SourceItemUpdateHistory
 *
 * @SuppressWarnings(PHPMD)
 */
class SourceItemUpdateHistory extends \Magento\Framework\Model\AbstractModel implements SourceItemUpdateHistoryInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'source_item_update_history';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'source_item_update_history';


    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'source_item_update_history';

    /**
     * @return void
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

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(SourceItemUpdateHistoryInterface::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($dataId)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::ID, $dataId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt() 
    {
        return $this->getData(SourceItemUpdateHistoryInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::CREATED_AT, $created);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt() {
        return $this->getData(SourceItemUpdateHistoryInterface::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastExecuted()
    {
        return $this->getData(SourceItemUpdateHistoryInterface::LAST_EXECUTED);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastExecuted($lastExecuted)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::LAST_EXECUTED, $lastExecuted);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsProcessing()
    {
        return $this->getData(SourceItemUpdateHistoryInterface::IS_PROCESSING);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsProcessing($isProcessing)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::IS_PROCESSING, $isProcessing);
    }

    /**
     * {@inheritdoc}
     */
    public function getFlag()
    {
        return $this->getData(SourceItemUpdateHistoryInterface::FLAG);
    }

    /**
     * {@inheritdoc}
     */
    public function setFlag($flag)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::FLAG, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload()
    {
        return $this->getData(SourceItemUpdateHistoryInterface::PAYLOAD);
    }

    /**
     * {@inheritdoc}
     */
    public function setPayload($payload)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::PAYLOAD,$payload);
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->getData(SourceItemUpdateHistoryInterface::SKU);
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::SKU, $sku);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceCode()
    {
        return $this->getData(SourceItemUpdateHistoryInterface::SOURCE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceCode($sourceCode)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::SOURCE_CODE, $sourceCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->getData(SourceItemUpdateHistoryInterface::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::QTY, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceItemStatus()
    {
        return $this->getData(SourceItemUpdateHistoryInterface::SOURCE_ITEM_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceItemStatus($status)
    {
        return $this->setData(SourceItemUpdateHistoryInterface::SOURCE_ITEM_STATUS, $status);
    }
}