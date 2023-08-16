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
use Trans\Reservation\Api\Data\SourceAttributeInterface;

interface SourceAttributeRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterface $data
     * @return \Trans\Reservation\Api\Data\SourceAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SourceAttributeInterface $data);

    /**
     * Save data.
     *
     * @param array $data
     * @return \Trans\Reservation\Api\Data\SourceAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveData(array $data);

    /**
     * Retrieve data
     *
     * @param string $attribute
     * @param string $sourceCode
     * @return \Trans\Reservation\Api\Data\SourceAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($attribute, $sourceCode);

    /**
     * Retrieve data by id
     *
     * @param int $dataId
     * @return \Trans\Reservation\Api\Data\SourceAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);

    /**
     * Retrieve data by source code
     *
     * @param string $code
     * @return \Trans\Reservation\Api\Data\SourceAttributeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBySourceCode($code);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Trans\Reservation\Api\Data\BankSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete data.
     *
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(SourceAttributeInterface $data);

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
