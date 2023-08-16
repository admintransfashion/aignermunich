<?php
/**
 * @category Trans
 * @package  Trans_CatalogMultisource
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CatalogMultisource\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface;

interface SourceItemUpdateHistoryRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface $data
     * @return \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SourceItemUpdateHistoryInterface $data);

    /**
     * Retrieve data by id
     *
     * @param int $dataId
     * @return \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);

    /**
     * Retrieve data by sku
     *
     * @param string $sku
     * @return \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBySku($sku);

    /**
     * Retrieve data by source code
     *
     * @param string $code
     * @return \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBySourceCode($code);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Trans\CatalogMultisource\Api\Data\BankSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete data.
     *
     * @param \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(SourceItemUpdateHistoryInterface $data);

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