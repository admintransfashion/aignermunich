<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use CTCD\Category\Api\Data\CategoryImportDataInterface;

/**
 * @interface CategoryImportDataRepositoryInterface
 */
interface CategoryImportDataRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \CTCD\Category\Api\Data\CategoryImportDataInterface $data
     * @return \CTCD\Category\Api\Data\CategoryImportDataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CategoryImportDataInterface $data);

    /**
     * Retrieve data by id
     *
     * @param int $dataId
     * @return \CTCD\Category\Api\Data\CategoryImportDataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);

    /**
     * Retrieve data by import id
     *
     * @param int $importId
     * @return \CTCD\Category\Api\Data\CategoryImportDataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByImportId($importId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \CTCD\Category\Api\Data\HotDealsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete data.
     *
     * @param \CTCD\Category\Api\Data\CategoryImportDataInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CategoryImportDataInterface $data);

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
