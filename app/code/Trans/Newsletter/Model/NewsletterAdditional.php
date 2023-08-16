<?php
/**
 * @category Trans
 * @package  Trans_Newsletter
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Newsletter\Model;

use Trans\Newsletter\Api\Data\NewsletterAdditionalInterface;
use Trans\Newsletter\Model\ResourceModel\NewsletterAdditional as ResourceModel;

/**
 * Class NewsletterAdditional
 */
class NewsletterAdditional extends \Magento\Framework\Model\AbstractModel implements NewsletterAdditionalInterface
{
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'trans_newsletter_additional';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_newsletter_additional';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_newsletter_additional';

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
        return $this->getData(NewsletterAdditionalInterface::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($idData)
    {
        $this->setData(NewsletterAdditionalInterface::ID, $idData);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscriberId()
    {
        return $this->getData(NewsletterAdditionalInterface::SUBSCRIBER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscriberId($subscriberId)
    {
        $this->setData(NewsletterAdditionalInterface::SUBSCRIBER_ID, $subscriberId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribeMen()
    {
        return $this->getData(NewsletterAdditionalInterface::SUBSCRIBE_MEN);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscribeMen($bool)
    {
        $this->setData(NewsletterAdditionalInterface::SUBSCRIBE_MEN, $bool);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribeWomen()
    {
        return $this->getData(NewsletterAdditionalInterface::SUBSCRIBE_WOMEN);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscribeWomen($bool)
    {
        $this->setData(NewsletterAdditionalInterface::SUBSCRIBE_WOMEN, $bool);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscribeCategory($category)
    {
        $this->setData(NewsletterAdditionalInterface::SUBSCRIBE_CATEGORY, $category);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribeCategory()
    {
        return $this->getData(NewsletterAdditionalInterface::SUBSCRIBE_CATEGORY);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(NewsletterAdditionalInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created)
    {
        $this->setData(NewsletterAdditionalInterface::CREATED_AT, $created);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(NewsletterAdditionalInterface::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(NewsletterAdditionalInterface::UPDATED_AT, $updatedAt);
        return $this;
    }
}
