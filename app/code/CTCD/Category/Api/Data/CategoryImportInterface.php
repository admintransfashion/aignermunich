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
 * @api CategoryImportInterface
 */
interface CategoryImportInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const ENTITY_ID = 'entity_id';
	const FILE = 'file';
	const STATUS = 'status';
	const ADMIN_ID = 'admin_id';
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

	/**
	 * constants for flag status
	 */
	const FLAG_PENDING = '1';
	const FLAG_PROGRESS = '2';
	const FLAG_FAIL = '3';
	const FLAG_SUCCESS = '5';

    const TABLE_NAME = 'category_import';

    const PREFIX_IMPORT_FILE = 'category_bulk_import';

	/**
	 * contsant for file path
	 */
	const FILE_PATH = 'ctcd/category_import/tmp/';

	/**
	 * Set csv file
	 *
	 * @param string $file
	 * @return $this
	 */
	public function setFile($file);

	/**
	 * Get csv file
	 *
	 * @return string
	 */
	public function getFile();

	/**
	 * Set status
	 *
	 * @param int $status
	 * @return $this
	 */
	public function setStatus($status);

	/**
	 * get status
	 *
	 * @return int
	 */
	public function getStatus();

    /**
     * Set uploader admin Id
     *
     * @param int $adminId
     * @return $this
     */
    public function setAdminId($adminId);

    /**
     * Get uploader admin Id
     *
     * @return int
     */
    public function getAdminId();

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
