<?php
/**
 * @category Trans
 * @package  Trans_Newsletter
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Newsletter\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\Newsletter\Api\Data\NewsletterAdditionalInterface;

/**
 *
 */
interface NewsletterAdditionalRepositoryInterface
{
    /**
     * Save data.
     *
     * @param \Trans\Newsletter\Api\Data\NewsletterInterface $data
     * @return \Trans\Newsletter\Api\Data\NewsletterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(NewsletterAdditionalInterface $data);

    /**
     * Retrieve data by id
     *
     * @param int $dataId
     * @return \Trans\Newsletter\Api\Data\NewsletterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($dataId);

    /**
     * Retrieve data by subscriber id
     *
     * @param int $subscriberId
     * @return \Trans\Newsletter\Api\Data\NewsletterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBySubscriberId($subscriberId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Trans\Newsletter\Api\Data\BankSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete data.
     *
     * @param \Trans\Newsletter\Api\Data\NewsletterInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(NewsletterAdditionalInterface $data);

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
