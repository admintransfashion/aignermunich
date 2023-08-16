<?php
/**
 * @category Trans
 * @package  Trans_CatalogMultisource
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CatalogMultisource\Api\Data;

/**
 * @api
 */
interface SourceItemUpdateHistoryInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'source_item_update_history';
	const ID = 'id';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const LAST_EXECUTED = 'last_executed';
	const IS_PROCESSING = 'is_processing';
	const FLAG = 'flag'; //success, error, hold(SKU doesn't exist)
	const PAYLOAD = 'payload';
	const SKU = 'sku';
	const SOURCE_CODE = 'source_code';
	const QTY = 'qty';
	const SOURCE_ITEM_STATUS = 'source_item_status';


	/**
	 * Constant for flag
	 */
	const FLAG_SUCCESS = 'success';
	const FLAG_HOLD = 'hold';
	const FLAG_CANCELLED = 'cancelled';
	const FLAG_FAILED = 'failed';

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
	 * Get last executed
	 * 
	 * @return string
	 */
	public function getLastExecuted();

	/**
	 * Set last executed
	 * 
	 * @param string $lastExecuted
	 * @return void
	 */
	public function setLastExecuted($lastExecuted);

	/**
	 * Get is processing
	 * 
	 * @return string
	 */
	public function getIsProcessing();

	/**
	 * Set is processing
	 * 
	 * @param string $isProcessing
	 * @return void
	 */
	public function setIsProcessing($isProcessing);

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
	 * Get payload
	 * 
	 * @return string
	 */
	public function getPayload();

	/**
	 * Set payload
	 * 
	 * @param string $payload
	 * @return void
	 */
	public function setPayload($payload);

	/**
	 * Get sku
	 * 
	 * @return string
	 */
	public function getSku();

	/**
	 * Set sku
	 * 
	 * @param string $sku
	 * @return void
	 */
	public function setSku($sku);

	/**
	 * Get source code
	 * 
	 * @return string
	 */
	public function getSourceCode();

	/**
	 * Set source code
	 * 
	 * @param string $sourceCode
	 * @return void
	 */
	public function setSourceCode($sourceCode);

	/**
	 * Get qty
	 * 
	 * @return string
	 */
	public function getQty();

	/**
	 * Set qty
	 * 
	 * @param string $qty
	 * @return void
	 */
	public function setQty($qty);

	/**
	 * Get source item status
	 * 
	 * @return string
	 */
	public function getSourceItemStatus();

	/**
	 * Set source item status
	 * 
	 * @param string $status
	 * @return void
	 */
	public function setSourceItemStatus($status);
}