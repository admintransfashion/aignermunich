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
interface UserStoreInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'user_admin_store';
	const ID = 'id';
	const STORE_CODE = 'store_code';
	const USER_ID = 'user_id';
	const CREATED_BY = 'created_by';
	const UPDATED_BY = 'updated_by';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

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
	 * Get user id
	 *
	 * @return int
	 */
	public function getUserId();

	/**
	 * Set user id
	 *
	 * @param string $userId
	 * @return void
	 */
	public function setUserId($userId);

	/**
	 * Get store code
	 *
	 * @return string
	 */
	public function getStoreCode();

	/**
	 * Set store code
	 *
	 * @param string $code
	 * @return void
	 */
	public function setStoreCode($code);

	/**
	 * Get created by
	 *
	 * @return string
	 */
	public function getCreatedBy();

	/**
	 * Set created by
	 *
	 * @param string $createdBy
	 * @return void
	 */
	public function setCreatedBy($createdBy);

	/**
	 * Get updated by
	 *
	 * @return string
	 */
	public function getUpdatedBy();

	/**
	 * Set updated by
	 *
	 * @param string $updatedBy
	 * @return void
	 */
	public function setUpdatedBy($updatedBy);

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
