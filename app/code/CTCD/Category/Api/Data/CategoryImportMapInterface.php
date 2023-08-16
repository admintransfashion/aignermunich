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

namespace CTCD\Category\Api\Data;

/**
 * @api CategoryImportMapInterface
 */
interface CategoryImportMapInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID = 'entity_id';
	const CATEGORY_CODE = 'category_code';
	const CATEGORY_ID = 'category_id';
	const OFFLINE_ID = 'offline_id';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

    const TABLE_NAME = 'category_import_map';

	/**
	 * Set category code
	 *
	 * @param string $code
	 * @return $this
	 */
	public function setCategoryCode($code);

	/**
	 * Get category code
	 *
	 * @return string
	 */
	public function getCategoryCode();

	/**
	 * Set category id
	 *
	 * @param int $categoryId
	 * @return $this
	 */
	public function setCategoryId($categoryId);

	/**
	 * get category id
	 *
	 * @return int
	 */
	public function getCategoryId();

	/**
	 * Set offline id
	 *
	 * @param int $offlineId
	 * @return $this
	 */
	public function setOfflineId($offlineId);

	/**
	 * get offline id
	 *
	 * @return int
	 */
	public function getOfflineId();

	/**
	 * Set created at
	 *
	 * @param string $created
	 * @return $this
	 */
	public function setCreatedAt($created);

	/**
	 * Set updated at
	 *
	 * @param string $updated
	 * @return $this
	 */
	public function setUpdatedAt($updated);
}
