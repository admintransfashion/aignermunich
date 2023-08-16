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
use Trans\Reservation\Api\Data\ReservationAttributeInterface;

interface ReservationAttributeRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\ReservationAttributeInterface\Api\Data\ReservationAttributeInterface $data
     * @return \Trans\ReservationAttributeInterface\Api\Data\ReservationAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(ReservationAttributeInterface $data);

    /**
     * Retrieve data
     *
     * @param int $attribute
     * @param int $reservationId
     * @return \Trans\ReservationAttributeInterface\Api\Data\ReservationAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($attribute, $reservationId);

    /**
     * Retrieve data by id
     *
     * @param int $dataId
     * @return \Trans\ReservationAttributeInterface\Api\Data\ReservationAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);

    /**
     * Retrieve data by reservation id
     *
     * @param int $reservationId
     * @return \Trans\ReservationAttributeInterface\Api\Data\ReservationAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByReservationId($reservationId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Trans\ReservationAttributeInterface\Api\Data\BankSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete data.
     *
     * @param \Trans\ReservationAttributeInterface\Api\Data\ReservationAttributeInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ReservationAttributeInterface $data);

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
     * generate reservation number
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateReservationNumber();
}
