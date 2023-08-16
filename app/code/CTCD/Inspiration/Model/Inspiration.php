<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Model;

use Magento\Framework\Model\AbstractModel;
use CTCD\Inspiration\Api\Data\InspirationInterface;
use CTCD\Inspiration\Model\ResourceModel\Inspiration as InspirationResourceModel;

class Inspiration extends AbstractModel implements InspirationInterface
{
    /**
     * @var string
     */
    const CACHE_TAG = 'ctcd_inspiration';

    /**
     * @var string
     */
    protected $_eventPrefix = self::CACHE_TAG;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(InspirationResourceModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return (string) $this->getData(InspirationInterface::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return (string) $this->getData(InspirationInterface::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlKey()
    {
        return (string) $this->getData(InspirationInterface::URL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return (string) $this->getData(InspirationInterface::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function getHistory()
    {
        return (string) $this->getData(InspirationInterface::HISTORY);
    }

    /**
     * {@inheritdoc}
     */
    public function getIncludeInMenu()
    {
        return (bool) $this->getData(InspirationInterface::INCLUDE_MENU);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return (bool) $this->getData(InspirationInterface::ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->setData(InspirationInterface::TITLE, $title);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->setData(InspirationInterface::CONTENT, $content);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlKey($urlKey)
    {
        $this->setData(InspirationInterface::URL_KEY, $urlKey);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($order)
    {
        $this->setData(InspirationInterface::SORT_ORDER, $order);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHistory($history)
    {
        $this->setData(InspirationInterface::HISTORY, $history);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncludeInMenu($flag)
    {
        $this->setData(InspirationInterface::INCLUDE_MENU, $flag);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($flag)
    {
        $this->setData(InspirationInterface::ACTIVE, $flag);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($date)
    {
        $this->setData(InspirationInterface::CREATED_AT, $date);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($date)
    {
        $this->setData(InspirationInterface::UPDATED_AT, $date);
        return $this;
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
}
