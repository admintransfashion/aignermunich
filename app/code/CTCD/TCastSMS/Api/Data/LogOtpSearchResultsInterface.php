<?php
/**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_TCastSMS
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\TCastSMS\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface LogOtpSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get item list.
     *
     * @return \CTCD\TCastSMS\Api\Data\LogOtpInterface[]
     */
    public function getItems();

    /**
     * Set item list.
     *
     * @param \CTCD\TCastSMS\Api\Data\LogOtpInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
