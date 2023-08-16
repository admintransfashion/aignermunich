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

namespace Trans\Reservation\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface SourceAttributeSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get item list.
     *
     * @return \Trans\Reservation\Api\Data\SourceAttributeInterface[]
     */
    public function getItems();

    /**
     * Set item list.
     *
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
