<?php
/**
 * @category Trans
 * @package  Trans_Catalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Api\Data;

/**
 * @api
 */
interface SeasonInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'master_season';
	const ID = 'id';
	const CODE = 'code';
	const LABEL = 'label';
	const DESC = 'description';
	const FLAG = 'flag';
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
	 * Get code
	 * 
	 * @return string
	 */
	public function getCode();

	/**
	 * Set code
	 * 
	 * @param string $code
	 * @return void
	 */
	public function setCode($code);

	/**
	 * Get label
	 * 
	 * @return string
	 */
	public function getLabel();

	/**
	 * Set label
	 * 
	 * @param string $label
	 * @return void
	 */
	public function setLabel($label);

	/**
	 * Get description
	 * 
	 * @return string
	 */
	public function getDesc();

	/**
	 * Set description
	 * 
	 * @param string $desc
	 * @return void
	 */
	public function setDesc($desc);

	/**
	 * Get flag
	 * 
	 * @return bool
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
