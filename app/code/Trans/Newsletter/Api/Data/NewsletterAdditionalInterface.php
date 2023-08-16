<?php
/**
 * @category Trans
 * @package  Trans_Newsletter
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Newsletter\Api\Data;

/**
 * @api
 */
interface NewsletterAdditionalInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'newsletter_subscriber_additional';
	const ID = 'id';
	const SUBSCRIBER_ID = 'subscriber_id';
	const SUBSCRIBE_MEN = 'subscribe_men';
	const SUBSCRIBE_WOMEN = 'subscribe_women';
    const SUBSCRIBE_CATEGORY = 'subscribe_category';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	const SUBSCRIBER_MEN = 1;
	const SUBSCRIBER_WOMEN = 2;

	const SUBSCRIBE_CATEGORY_ALL = 'All';
    const SUBSCRIBE_CATEGORY_MEN = 'Men';
    const SUBSCRIBE_CATEGORY_WOMEN = 'Women';

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId();

	/**
	 * Set id
	 *
	 * @param string $idData
	 * @return void
	 */
	public function setId($idData);

	/**
	 * Get subscriber id
	 *
	 * @return int
	 */
	public function getSubscriberId();

	/**
	 * Set subscriber id
	 *
	 * @param string $userId
	 * @return $this
	 */
	public function setSubscriberId($subscriberId);

	/**
	 * is subscribe men
	 *
	 * @return bool
	 */
	public function isSubscribeMen();

	/**
	 * Set subscribe men
	 *
	 * @param bool $bool
	 * @return $this
	 */
	public function setSubscribeMen($bool);

	/**
	 * is subscribe woman
	 *
	 * @return bool
	 */
	public function isSubscribeWomen();

    /**
     * Set subscribe women
     *
     * @param bool $bool
     * @return $this
     */
    public function setSubscribeWomen($bool);

    /**
     * Set subscribe category
     *
     * @param string $category
     * @return $this
     */
    public function setSubscribeCategory($category);

    /**
     * get subscribe category
     *
     * @return string
     */
    public function getSubscribeCategory();

	/**
	 * Get created at
	 *
	 * @return string
	 */
	public function getCreatedAt();

	/**
	 * Set created at
	 *
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($created);

	/**
	 * Get updated at
	 *
	 * @return string
	 */
	public function getUpdatedAt();

	/**
	 * Set updated at
	 *
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt);
}
