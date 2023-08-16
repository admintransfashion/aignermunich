<?php
/**
 * @category Trans
 * @package  Trans_Catalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Model;

use Trans\Catalog\Api\Data\SeasonInterface;
use Trans\Catalog\Model\ResourceModel\Season as ResourceModel;

/**
 * Class Season
 */
class Season extends \Magento\Framework\Model\AbstractModel implements SeasonInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'trans_season';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_season';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_season';

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
        return $this->getData(SeasonInterface::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($dataId)
    {
        return $this->setData(SeasonInterface::ID, $dataId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(SeasonInterface::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(SeasonInterface::CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData(SeasonInterface::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        return $this->setData(SeasonInterface::LABEL, $label);
    }

    /**
     * {@inheritdoc}
     */
    public function getDesc()
    {
        return $this->getData(SeasonInterface::DESC);
    }

    /**
     * {@inheritdoc}
     */
    public function setDesc($desc)
    {
        return $this->setData(SeasonInterface::DESC, $desc);
    }

    /**
     * {@inheritdoc}
     */
    public function getFlag()
    {
        return $this->getData(SeasonInterface::FLAG);
    }

    /**
     * {@inheritdoc}
     */
    public function setFlag($flag)
    {
        return $this->setData(SeasonInterface::FLAG, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt() 
    {
        return $this->getData(SeasonInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created)
    {
        return $this->setData(SeasonInterface::CREATED_AT, $created);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt() {
        return $this->getData(SeasonInterface::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(SeasonInterface::UPDATED_AT, $updatedAt);
    }
}
