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
interface ReservationInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'reservation';
	const ID = 'id';
    const FLAG = 'flag';
    const CUSTOMER_ID = 'customer_id';
    const RESERVATION_NUMBER = 'reservation_number';
    const RESERVATION_DATE_SUBMIT = 'reservation_date_submit';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

    /**
     * Old fields - not used since 1.3.0
     */
    const SOURCE_CODE = 'source_code';
    const TIME_START = 'reservation_time_start';
    const TIME_END = 'reservation_time_end';
    const OMS_REFERENCE_NUMBER = 'oms_reference_number';
    const RESERVATION_DATE_END = 'reservation_date_end';
    const RESERVATION_DATE_CONFIRM = 'reservation_date_confirm';

	/**
	 * Constant for flag
	 */
	const FLAG_NEW = 'new'; //when customer add product to reserve cart
	const FLAG_SUBMIT = 'submit'; //when customer submit products to reserve
	const FLAG_CANCEL = 'cancel'; //when customer not coming to store after time limit
	const FLAG_FINISH = 'finish'; //when customer already come to store
	const FLAG_ACTIVE_ARRAY = [self::FLAG_SUBMIT];
	const FLAG_ACTIVE = 'active';
	const FLAG_INACTIVE = 'inactive';

	const PREFIX_STORE_CODE = 'EAG';

	/**
	 * Get customer id
	 *
	 * @return int
	 */
	public function getCustomerId();

	/**
	 * Set customer id
	 *
	 * @param int $customerId
	 * @return $this
	 */
	public function setCustomerId(int $customerId);

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
     * Get reservation number
     *
     * @return string
     */
    public function getReservationNumber();

    /**
     * Set reservation number
     *
     * @param string $referenceNumber
     * @return $this
     */
    public function setReservationNumber($referenceNumber);

    /**
     * Get reservation date submit
     *
     * @return string
     */
    public function getReservationDateSubmit();

    /**
     * Set reservation date submit
     *
     * @param string $date
     * @return $this
     */
    public function setReservationDateSubmit($date);

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
