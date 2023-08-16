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
use Trans\Reservation\Api\Data\ReservationInterface;

interface ReservationRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\ReservationInterface\Api\Data\ReservationInterface $data
     * @return \Trans\ReservationInterface\Api\Data\ReservationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(ReservationInterface $data);

    /**
     * Retrieve data by id
     *
     * @param int $dataId
     * @return \Trans\ReservationInterface\Api\Data\ReservationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);

    /**
     * Retrieve data by customer id
     *
     * @param int $customerId
     * @return \Trans\ReservationInterface\Api\Data\ReservationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCustomerId($customerId);

    /**
     * Retrieve data by source code
     *
     * @param string $code
     * @return \Trans\ReservationInterface\Api\Data\ReservationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBySourceCode($code);

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
     * @param \Trans\ReservationInterface\Api\Data\ReservationInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ReservationInterface $data);

    /**
     * Delete data by ID.
     *
     * @param int $dataId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($dataId);
}
