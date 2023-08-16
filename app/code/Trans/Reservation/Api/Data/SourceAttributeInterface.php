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
interface SourceAttributeInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'source_attribute';
	const ID = 'id';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const SOURCE_CODE = 'source_code';
	const ATTRIBUTE = 'attribute';
	const VALUE = 'value';

	/**
	 * Constant for flag
	 */
	const OPEN_HOUR_ATTR = 'hour_open';
	const CLOSE_HOUR_ATTR = 'hour_close';
	const CITY_ATTR = 'city';
	const CITY_ID_ATTR = 'city_id';
	const DISTRICT_ATTR = 'district';
	const DISTRICT_ID_ATTR = 'district_id';

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
	 * Get source code
	 *
	 * @return string
	 */
	public function getSourceCode();

	/**
	 * Set source code
	 *
	 * @param string $code
	 * @return void
	 */
	public function setSourceCode($code);

	/**
	 * Get attribute
	 *
	 * @return string
	 */
	public function getAttribute();

	/**
	 * Set attribute
	 *
	 * @param string $attribute
	 * @return void
	 */
	public function setAttribute($attribute);

	/**
	 * Get value
	 *
	 * @return mixed
	 */
	public function getValue();

	/**
	 * Set value
	 *
	 * @param mixed $value
	 * @return void
	 */
	public function setValue($value);

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
	 * Get Closing time
	 *
	 * @param string $sourceCode
	 * @return void
	 */
	public function getCloseTime($sourceCode);

	/**
	 * Get opening time
	 *
	 * @param string $sourceCode
	 * @return void
	 */
	public function getOpenTime($sourceCode);
}
