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
use Trans\Reservation\Api\Data\ReservationConfigInterface;

interface ReservationConfigRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\ReservationConfigInterface\Api\Data\ReservationConfigInterface $data
     * @return \Trans\ReservationConfigInterface\Api\Data\ReservationConfigInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(ReservationConfigInterface $data);

    /**
     * Retrieve data by id
     *
     * @param int $dataId
     * @return \Trans\ReservationConfigInterface\Api\Data\ReservationConfigInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);

    /**
     * Retrieve data by category id
     *
     * @param array $categoryids
     * @param string $config
     * @return \Trans\ReservationConfigInterface\Api\Data\ReservationConfigInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCategoryIds(array $categoryids, string $config);

    /**
     * Retrieve data by product skus
     *
     * @param array $productSkus
     * @param string $config
     * @return \Trans\ReservationConfigInterface\Api\Data\ReservationConfigInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByProductSkus(array $productSkus, string $config);

    /**
     * Retrieve data by value
     *
     * @param int $value
     * @param string $config
     * @return \Trans\ReservationConfigInterface\Api\Data\ReservationConfigInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByValue(int $value, string $config);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Trans\ReservationConfigInterface\Api\Data\BankSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete data.
     *
     * @param \Trans\ReservationConfigInterface\Api\Data\ReservationConfigInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ReservationConfigInterface $data);

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
