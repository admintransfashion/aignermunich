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

namespace Trans\Catalog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\Catalog\Api\Data\SeasonInterface;

interface SeasonRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\Catalog\Api\Data\SeasonInterface $data
     * @return \Trans\Catalog\Api\Data\SeasonInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SeasonInterface $data);

    /**
     * Retrieve data by id
     *
     * @param int $dataId
     * @return \Trans\Catalog\Api\Data\SeasonInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);

    /**
     * Retrieve data by code
     *
     * @param string $code
     * @return \Trans\Catalog\Api\Data\SeasonInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCode($code);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Trans\Catalog\Api\Data\BankSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete data.
     *
     * @param \Trans\Catalog\Api\Data\SeasonInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(SeasonInterface $data);

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