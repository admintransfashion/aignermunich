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
 * @api CategoryImportWaitInterface
 */
interface CategoryImportWaitInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID = 'entity_id';
	const JSON_DATA = 'json_data';
	const JSON_KEYS = 'json_keys';
	const CODE = 'code';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

    const TABLE_NAME = 'category_import_waiting';

	/**
	 * Set json data
	 *
	 * @param string $data
	 * @return $this
	 */
	public function setJsonData($data);

	/**
	 * Get json data
	 *
	 * @return string
	 */
	public function getJsonData();

	/**
	 * Set json keys
	 *
	 * @param string $keys
	 * @return $this
	 */
	public function setJsonKeys($keys);

	/**
	 * get json keys
	 *
	 * @return string
	 */
	public function getJsonKeys();

	/**
	 * Set code
	 *
	 * @param string $code
	 * @return $this
	 */
	public function setCode($code);

	/**
	 * get code
	 *
	 * @return string
	 */
	public function getCode();

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
