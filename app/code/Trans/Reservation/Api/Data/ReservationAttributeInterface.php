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
interface ReservationAttributeInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'reservation_attribute';
	const ID = 'id';
    const RESERVATION_ID = 'reservation_id';
	const ATTRIBUTE = 'attribute';
    const VALUE = 'value';
	const FLAG = 'flag';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	/**
	 * Constant for attribute
	 */
	const ATTR_GLOBAL = 0;
	const FLAG_ACTIVE = 1;
	const FLAG_INACTIVE = 0;
	const LAST_RESERVATION_NUMBER_ATTR = 'last_reservation_number';

	/**
	 * Get reservation id
	 *
	 * @return int
	 */
	public function getReservationId();

	/**
	 * Set reservation id
	 *
	 * @param string $reservationId
	 * @return $this
	 */
	public function setReservationId($reservationId);

    /**
     * Get attribute
     *
     * @return string
     */
    public function getAttribute();

    /**
     * Set attribute
     *
     * @param int $attribute
     * @return $this
     */
    public function setAttribute($attribute);

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

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
     * @return $this
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
	 * @param string $datetime
	 * @return $this
	 */
	public function setCreatedAt($datetime);

	/**
	 * Get updated at
	 *
	 * @return string
	 */
	public function getUpdatedAt();

	/**
	 * Set updated at
	 *
	 * @param string $datetime
	 * @return $this
	 */
	public function setUpdatedAt($datetime);

}
