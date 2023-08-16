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

namespace CTCD\Inspiration\Api\Data;

/**
 * @api
 */
interface InspirationInterface
{
    const ENTITY_ID = 'entity_id';
    const TITLE = 'title';
    const CONTENT = 'content';
    const URL_KEY = 'url_key';
    const SORT_ORDER = 'sort_order';
    const HISTORY = 'history';
    const INCLUDE_MENU = 'include_in_menu';
    const ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();

    /**
     * Get url key
     *
     * @return string
     */
    public function getUrlKey();

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Get history
     *
     * @return string
     */
    public function getHistory();

    /**
     * Get include in menu
     *
     * @return boolean
     */
    public function getIncludeInMenu();

    /**
     * Get is active
     *
     * @return boolean
     */
    public function getIsActive();

    /**
     * Set title
     *
     * @param string $title
     * @return this
     */
    public function setTitle($title);

    /**
     * Set content
     *
     * @param string $content
     * @return this
     */
    public function setContent($content);

    /**
     * Set url key
     *
     * @param string $urlKey
     * @return this
     */
    public function setUrlKey($urlKey);

    /**
     * Set sort order
     *
     * @param string $order
     * @return this
     */
    public function setSortOrder($order);

    /**
     * Set history
     *
     * @param string $history
     * @return this
     */
    public function setHistory($history);

    /**
     * Set include in menu
     *
     * @param boolean $flag
     * @return $this
     */
    public function setIncludeInMenu($flag);

    /**
     * Set is active
     *
     * @param boolean $flag
     * @return $this
     */
    public function setIsActive($flag);

    /**
     * set Date Created
     *
     * @param string $date
     * @return $this
     */
    public function setCreatedAt($date);

    /**
     * Set Updated Date
     *
     * @param string $date
     * @return $this
     */
    public function setUpdatedAt($date);
}
