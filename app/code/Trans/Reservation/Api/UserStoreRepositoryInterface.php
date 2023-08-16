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
use Trans\Reservation\Api\Data\UserStoreInterface;

interface UserStoreRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\ReservationInterface\Api\Data\UserStoreInterface $data
     * @return \Trans\ReservationInterface\Api\Data\UserStoreInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(UserStoreInterface $data);

    /**
     * Retrieve data by id
     *
     * @param int $dataId
     * @return \Trans\ReservationInterface\Api\Data\UserStoreInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);

    /**
     * Retrieve data by user id
     *
     * @param int $userId
     * @return \Trans\ReservationInterface\Api\Data\UserStoreInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByUserId($userId);

    /**
     * Retrieve data by store code
     *
     * @param string $code
     * @return \Trans\ReservationInterface\Api\Data\UserStoreInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByStoreCode($code);

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
     * @param \Trans\ReservationInterface\Api\Data\UserStoreInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(UserStoreInterface $data);

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
