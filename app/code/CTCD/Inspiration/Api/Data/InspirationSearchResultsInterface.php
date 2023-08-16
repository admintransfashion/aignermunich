<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface InspirationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get item list.
     *
     * @return \CTCD\Inspiration\Api\Data\InspirationInterface[]
     */
    public function getItems();

    /**
     * Set item list.
     *
     * @param \CTCD\Inspiration\Api\Data\InspirationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
