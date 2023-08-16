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

namespace Trans\Reservation\Api\Data;

/**
 * @api
 */
interface ReservationConfigInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'reservation_product_config';
	const ID = 'id';
	const CONFIG = 'config';
	const VALUE = 'value';
	const TITLE = 'title';
	const FILTER = 'filter';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const FLAG = 'flag';
	const PRODUCT_SKU = 'product_skus';
	const CATEGORY_ID = 'category_ids';
	const STORE_CODE = 'store_code';

	/**
	 * Constant for flag
	 */
	const FLAG_ACTIVE = 1;
	const FLAG_INACTIVE = 0;

	/**
	 * Constant for filter
	 */
	const FILTER_CATEGORY = 'category';
	const FILTER_PRODUCT = 'product';
	const FILTER_STORE = 'store';
	const FILTER_ALL = 'all';

	/**
	 * Constant for config
	 */
	const CONFIG_BUFFER = 'buffer';
	const CONFIG_MAXQTY = 'maxqty';
	const CONFIG_HOURS = 'hours';
	const CONFIG_DATE = 'date';

	/**
	 * Constant for config label
	 */
	const CONFIG_BUFFER_LABEL = 'Buffer';
	const CONFIG_MAXQTY_LABEL = 'Max Qty';
	const CONFIG_HOURS_LABEL = 'Hours';
	const CONFIG_DATE_LABEL = 'Days';

	/**
	 * Constant for config priority
	 */
	const CONFIG_PRIORITY_MOST = 1;
	const CONFIG_PRIORITY_FEWEST = 2;
	const CONFIG_PRIORITY_LATEST = 3;
	const CONFIG_PRIORITY_LONGEST = 4;
	const CONFIG_PRIORITY_SHORTEST = 5;

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
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle();

	/**
	 * Set title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title);

	/**
	 * Get config
	 *
	 * @return string
	 */
	public function getConfig();

	/**
	 * Set config
	 *
	 * @param string $config
	 * @return void
	 */
	public function setConfig($config);

	/**
	 * Get category id
	 *
	 * @return int
	 */
	public function getCategoryId();

	/**
	 * Set category id
	 *
	 * @param string $categoryId
	 * @return void
	 */
	public function setCategoryId($categoryId);

	/**
	 * Get value
	 *
	 * @return int
	 */
	public function getValue();

	/**
	 * Set value
	 *
	 * @param string $value
	 * @return void
	 */
	public function setValue($value);

	/**
	 * Get product id
	 *
	 * @return int
	 */
	public function getProductSku();

	/**
	 * Set product sku
	 *
	 * @param string $productSku
	 * @return void
	 */
	public function setProductSku($productSku);

	/**
	 * Get filter
	 *
	 * @return string
	 */
	public function getFilter();

	/**
	 * Set filter
	 *
	 * @param string $filter
	 * @return void
	 */
	public function setFilter($filter);

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

	/**
	 * Get flag
	 *
	 * @return string
	 */
	public function getFlag();

	/**
	 * Set flag
	 *
	 * @param string $flag
	 * @return void
	 */
	public function setFlag($flag);

	/**
	 * Get store
	 *
	 * @return string
	 */
	public function getStore();

	/**
	 * Set store
	 *
	 * @param string $store
	 * @return void
	 */
	public function setStore($store);
}
