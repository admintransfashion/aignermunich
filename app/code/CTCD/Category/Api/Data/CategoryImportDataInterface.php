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
 * @api CategoryImportDataInterface
 */
interface CategoryImportDataInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID = 'entity_id';
	const IMPORT_ID = 'import_id';
	const JSON_DATA = 'json_data';
	const STATUS = 'status';
	const SEQUENCE = 'sequence';
	const COLUMN = 'column';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	/**
	 * constants for flag status
	 */
	const FLAG_PENDING = '1';
	const FLAG_PROGRESS = '3';
	const FLAG_SUCCESS = '5';

    const TABLE_NAME = 'category_import_data';

	/**
     * const csv field
     */
    const CODE = 'category_code';
    const CATEGORY_PARENT = 'parent';
    const IS_ACTIVE = 'is_active';
    const INCLUDE_IN_MENU = 'include_in_menu';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const IMAGE = 'image';
    const DISPLAY_MODE = 'display_mode';
    const IS_ANCHOR = 'is_anchor';
    const AVAILABLE_SORT_BY = 'available_sort_by';
    const DEFAULT_SORT_BY = 'default_sort_by';
    const URL_KEY = 'url_key';
    const META_TITLE = 'meta_title';
    const META_KEYWORDS = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    const MAP_OFFLINE = 'map_offline';

	/**
	 * contsant for file path
	 */
	const IMAGE_PATH = 'ctcd/category_import/img';
	const CATEGORY_IMAGE_PATH = 'catalog/category/images';

	/**
	 * Set import id
	 *
	 * @param int $importId
	 * @return $this
	 */
	public function setImportId($importId);

	/**
	 * Get import id
	 *
	 * @return int
	 */
	public function getImportId();

	/**
	 * Set JSON data
	 *
	 * @param string $data
	 * @return $this
	 */
	public function setJsonData($data);

	/**
	 * get JSON data
	 *
	 * @return string
	 */
	public function getJsonData();

	/**
	 * Set sequence
	 *
	 * @param int $seq
	 * @return $this
	 */
	public function setSequence($seq);

	/**
	 * get sequence
	 *
	 * @return int
	 */
	public function getSequence();

	/**
	 * Get status
	 *
	 * @return int
	 */
	public function getStatus();

	/**
	 * Set status
	 *
	 * @param int $status
	 * @return $this
	 */
	public function setStatus($status);

	/**
	 * Get column
	 *
	 * @return string
	 */
	public function getColumn();

	/**
	 * Set column
	 *
	 * @param string $column
	 * @return $this
	 */
	public function setColumn($column);

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
