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
use CTCD\Category\Api\Data\CategoryImportWaitInterface;

/**
 * @interface CategoryImportWaitRepositoryInterface
 */
interface CategoryImportWaitRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \CTCD\Category\Api\Data\CategoryImportWaitInterface $data
     * @return \CTCD\Category\Api\Data\CategoryImportWaitInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CategoryImportWaitInterface $data);

    /**
     * Retrieve data by id
     *
     * @param int $dataId
     * @return \CTCD\Category\Api\Data\CategoryImportWaitInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);


    /**
     * Retrieve data by code
     *
     * @param int $code
     * @return \CTCD\Category\Api\Data\CategoryImportWaitInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCode($code);

    /**
     * Retrieve limited collection data
     *
     * @return \CTCD\Category\Model\ResourceModel\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWaitingData();

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
     * @param \CTCD\Category\Api\Data\CategoryImportWaitInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CategoryImportWaitInterface $data);
}
