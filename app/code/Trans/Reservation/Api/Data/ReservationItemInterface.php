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
interface ReservationItemInterface
{
	/**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
	const TABLE_NAME = 'reservation_item';
	const ID = 'id';
	const RESERVATION_ID = 'reservation_id';
    const REFERENCE_NUMBER = 'reference_number';
    const SOURCE_CODE = 'source_code';
    const PRODUCT_ID = 'product_id';
    const BASE_PRICE = 'base_price';
    const FINAL_PRICE = 'final_price';
    const QTY = 'qty';
    const BUFFER = 'buffer';
    const MAXQTY = 'maxqty';
    const START_DATE = 'reservation_date_start';
	const START_TIME = 'reservation_time_start';
    const END_DATE = 'reservation_date_end';
	const END_TIME = 'reservation_time_end';
    const FLAG = 'flag';
    const BUSINESS_STATUS = 'business_status';
	const REMINDER_EMAIL = 'reminder_email';
	const AUTOCANCEL_EMAIL = 'autocancel_email';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Old fields - not used since 1.3.0
     */
    const CREATE_ORDER_OMS = 'create_order_oms';
	/**
	 * Constant for flag
	 */
	const FLAG_SUBMIT = 'submit';
	const FLAG_CANCEL = 'cancel';
	const FLAG_CONFIRM = 'confirm';
	const FLAG_NEW = 'new';

	/**
	 * Reminder email flag
	 */
	const REMINDER_EMAIL_TRUE = 1;
	const REMINDER_EMAIL_FALSE = 0;

	/**
	 * Autocancel email flag
	 */
	const AUTOCANCEL_EMAIL_TRUE = 1;
	const AUTOCANCEL_EMAIL_FALSE = 0;

	/**
	 * Constant for business status
	 */
	const BUSINESS_STATUS_RESERVE = 'reserve';
	const BUSINESS_STATUS_VISIT = 'visit';
	const BUSINESS_STATUS_PURCHASE = 'purchase';
	const BUSINESS_STATUS_CHANGE = 'change';
	const BUSINESS_STATUS_CANCELED = 'canceled';
	const BUSINESS_STATUS_VISIT_CANCELED = 'cancel_business';

	/**
	 * Constant for business status label
	 */
	const BUSINESS_STATUS_RESERVE_LABEL = 'Waiting For Pickup';
	const BUSINESS_STATUS_VISIT_LABEL = 'Visited';
	const BUSINESS_STATUS_PURCHASE_LABEL = 'Made Purchase';
	const BUSINESS_STATUS_CHANGE_LABEL = 'Change Product';
	const BUSINESS_STATUS_CANCELED_LABEL = 'Cancelled';

	/**
	 * Get reservation id
	 *
	 * @return int
	 */
	public function getReservationId();

	/**
	 * Set reservation id
	 *
	 * @param int $reservationId
	 * @return $this
	 */
	public function setReservationId($reservationId);

    /**
     * Get reference number
     *
     * @return string
     */
    public function getReferenceNumber();

    /**
     * Set reference number
     *
     * @param string $referenceNumber
     * @return $this
     */
    public function setReferenceNumber($referenceNumber);

    /**
     * Get source code
     *
     * @return string
     */
    public function getSourceCode();

    /**
     * Set source code
     *
     * @param string $source
     * @return $this
     */
    public function setSourceCode($source);

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId();

    /**
     * Set Product Id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get base/original price
     *
     * @return float
     */
    public function getBasePrice();

    /**
     * Set base/original price
     *
     * @param float $price
     * @return $this
     */
    public function setBasePrice($price);

    /**
     * Get final price
     *
     * @return float
     */
    public function getFinalPrice();

    /**
     * Set final price
     *
     * @param float $price
     * @return $this
     */
    public function setFinalPrice($price);

	/**
	 * Get qty
	 *
	 * @return int
	 */
	public function getQty();

	/**
	 * Set qty
	 *
	 * @param int $qty
	 * @return $this
	 */
	public function setQty($qty);

    /**
     * Get buffer
     *
     * @return int
     */
    public function getBuffer();

    /**
     * Set Buffer
     *
     * @param int $buffer
     * @return $this
     */
    public function setBuffer($buffer);

    /**
     * Get max qty
     *
     * @return int
     */
    public function getMaxqty();

    /**
     * Set max qty
     *
     * @param int $maxqty
     * @return $this
     */
    public function setMaxqty($maxqty);

    /**
     * Get start date
     *
     * @return date
     */
    public function getReservationDateStart();

    /**
     * Set start date
     *
     * @param string $date
     * @return $this
     */
    public function setReservationDateStart($date);

    /**
     * Get start time
     *
     * @return string
     */
    public function getReservationTimeStart();

    /**
     * Set start time
     *
     * @param string $time
     * @return $this
     */
    public function setReservationTimeStart($time);

    /**
     * Get end date
     *
     * @return date
     */
    public function getReservationDateEnd();

    /**
     * Set end date
     *
     * @param string $date
     * @return $this
     */
    public function setReservationDateEnd($date);

	/**
	 * Get end time
	 *
	 * @return string
	 */
	public function getReservationTimeEnd();

	/**
	 * Set end time
	 *
	 * @param string $time
	 * @return $this
	 */
	public function setReservationTimeEnd($time);

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
     * Get business status
     *
     * @return string
     */
    public function getBusinessStatus();

    /**
     * Set business status
     *
     * @param string $status
     * @return $this
     */
    public function setBusinessStatus($status);

	/**
	 * Is customer already get reminder email
	 *
	 * @return string
	 */
	public function getReminderEmail();

	/**
	 * Set reminder email status
	 *
	 * @param int $status
	 * @return $this
	 */
	public function setReminderEmail($status);

	/**
	 * Is customer already get auto cancel email
	 *
	 * @return string
	 */
	public function getAutocancelEmail();

	/**
	 * Set autocancel email status
	 *
	 * @param int $status
	 * @return $this
	 */
	public function setAutocancelEmail($status);

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
