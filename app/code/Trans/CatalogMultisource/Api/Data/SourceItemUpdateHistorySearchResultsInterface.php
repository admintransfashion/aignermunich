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

namespace Trans\CatalogMultisource\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface SourceItemUpdateHistorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get item list.
     *
     * @return \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface[]
     */
    public function getItems();

    /**
     * Set item list.
     *
     * @param \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}