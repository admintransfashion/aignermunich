<?php
/**
 * @category Trans
 * @package  Trans_Newsletter
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Newsletter\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface NewsletterAdditionalSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get item list.
     *
     * @return \Trans\Newsletter\Api\Data\NewsletterAdditionalInterface[]
     */
    public function getItems();

    /**
     * Set item list.
     *
     * @param \Trans\Newsletter\Api\Data\NewsletterAdditionalInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}