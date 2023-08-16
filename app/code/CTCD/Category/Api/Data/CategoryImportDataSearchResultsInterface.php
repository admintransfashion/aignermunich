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

namespace CTCD\Category\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface CategoryImportDataSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get item list.
     *
     * @return \CTCD\Category\Api\Data\CategoryImportDataInterface[]
     */
    public function getItems();

    /**
     * Set item list.
     *
     * @param \CTCD\Category\Api\Data\CategoryImportDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
