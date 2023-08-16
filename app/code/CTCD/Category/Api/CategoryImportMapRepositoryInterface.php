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
use CTCD\Category\Api\Data\CategoryImportMapInterface;

/**
 * @interface CategoryImportMapRepositoryInterface
 */
interface CategoryImportMapRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \CTCD\Category\Api\Data\CategoryImportMapInterface $data
     * @return \CTCD\Category\Api\Data\CategoryImportMapInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CategoryImportMapInterface $data);

    /**
     * Retrieve data by code
     *
     * @param int $code
     * @return \CTCD\Category\Api\Data\CategoryImportMapInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCode($code);

    /**
     * Retrieve category data by code
     *
     * @param int $code
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryByCode($code);

    /**
     * Retrieve data by category id
     *
     * @param int $categoryId
     * @return \CTCD\Category\Api\Data\CategoryImportMapInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCategoryId($categoryId);

    /**
     * Retrieve data by offline map
     *
     * @param string $offlineMap
     * @return \CTCD\Category\Api\Data\CategoryImportMapInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByOfflineMap($offlineMap);

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
     * @param \CTCD\Category\Api\Data\CategoryImportMapInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CategoryImportMapInterface $data);
}
