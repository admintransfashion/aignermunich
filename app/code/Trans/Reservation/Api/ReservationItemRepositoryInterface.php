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

namespace Trans\Reservation\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;

interface ReservationItemRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\ReservationInterface\Api\Data\ReservationItemInterface $data
     * @return \Trans\ReservationInterface\Api\Data\ReservationItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(ReservationItemInterface $data);

    /**
     * Retrieve data by reservation id & product id
     *
     * @param int $reservationId
     * @param int $productId
     * @return \Trans\ReservationInterface\Api\Data\ReservationItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($reservationId, $productId);

    /**
     * Retrieve data by reservation id & sku
     *
     * @param int $dataId
     * @return \Trans\ReservationInterface\Api\Data\ReservationItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);

    /**
     * Retrieve data by reservation id
     *
     * @param int $dataId
     * @return \Trans\ReservationInterface\Api\Data\ReservationItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByReservationId($dataId);

    /**
     * Retrieve data by reference number (oms order id)
     *
     * @param int $orderid
     * @return \Trans\ReservationInterface\Api\Data\ReservationItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByReference($orderid);

    /**
     * Retrieve data by reference number (oms order id) and items id
     *
     * @param string $orderid
     * @param array $items
     * @return \Trans\ReservationInterface\Api\Data\ReservationItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByReferenceItemIds(string $orderId, array $items);

    /**
     * Retrieve data by source code
     *
     * @param string $code
     * @return \Trans\ReservationInterface\Api\Data\ReservationItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBySku($code);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Trans\ReservationInterface\Api\Data\BankSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete data.
     *
     * @param \Trans\ReservationInterface\Api\Data\ReservationItemInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ReservationItemInterface $data);

    /**
     * Delete data by ID.
     *
     * @param int $dataId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($dataId);

    /**
     * is current item qty addable.
     *
     * @param int $reservationId
     * @param string $sourceCode
     * @param int $productId
     * @param int $qty
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isItemAddable($reservationId, $sourceCode, $productId = null, $qty = null);
}
