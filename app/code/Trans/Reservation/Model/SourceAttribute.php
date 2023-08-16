<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Reservation
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Reservation\Model;

use Trans\Reservation\Api\Data\SourceAttributeInterface;
use Trans\Reservation\Model\ResourceModel\SourceAttribute as ResourceModel;

/**
 * Class SourceAttribute
 */
class SourceAttribute extends \Magento\Framework\Model\AbstractModel implements SourceAttributeInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'trans_source_attribute';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_source_attribute';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_source_attribute';

    /**
     * @var \Trans\Reservation\Api\SourceAttributeRepositoryInterface
     */
    protected $sourceAttributeRepository;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param \Trans\Reservation\Api\SourceAttributeRepositoryInterface $sourceAttributeRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Trans\Reservation\Api\SourceAttributeRepositoryInterface $sourceAttributeRepository,
        array $data = []
    ) {
        $this->sourceAttributeRepository = $sourceAttributeRepository;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_construct();
    }

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
        return $this->getData(SourceAttributeInterface::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($dataId)
    {
        return $this->setData(SourceAttributeInterface::ID, $dataId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceCode()
    {
        return $this->getData(SourceAttributeInterface::SOURCE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceCode($code)
    {
        return $this->setData(SourceAttributeInterface::SOURCE_CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(SourceAttributeInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created)
    {
        return $this->setData(SourceAttributeInterface::CREATED_AT, $created);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt() {
        return $this->getData(SourceAttributeInterface::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(SourceAttributeInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute()
    {
        return $this->getData(SourceAttributeInterface::ATTRIBUTE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($attribute)
    {
        return $this->setData(SourceAttributeInterface::ATTRIBUTE, $attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData(SourceAttributeInterface::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(SourceAttributeInterface::VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCloseTime($sourceCode)
    {
        return $this->getCustomAttribute($sourceCode, SourceAttributeInterface::CLOSE_HOUR_ATTR);
    }

    /**
     * {@inheritdoc}
     */
    public function getOpenTime($sourceCode)
    {
        return $this->getCustomAttribute($sourceCode, SourceAttributeInterface::OPEN_HOUR_ATTR);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttribute($sourceCode, $attribute)
    {
        $sourceAttr = $this->sourceAttributeRepository->get($attribute, $sourceCode);
        return $sourceAttr->getValue();
    }
}
