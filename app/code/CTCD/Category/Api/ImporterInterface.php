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
use CTCD\Category\Api\Data\CategoryImportInterface;

/**
 * @interface ImporterInterface
 */
interface ImporterInterface
{
    /**
     * Import data.
     *
     * @param \CTCD\Category\Api\Data\CategoryImportInterface $data
     * @return \CTCD\Category\Api\Data\CategoryImportInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importData(CategoryImportInterface $data);

    /**
     * Late import data.
     *
     * @param \CTCD\Category\Model\ResourceModel\CategoryImportWait\Collection $data
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function lateImportData(\CTCD\Category\Model\ResourceModel\CategoryImportWait\Collection $data);
}
