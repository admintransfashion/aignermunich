<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Api\Quote\Data;

use Magento\Quote\Api\Data\EstimateAddressInterface as MagentoDataEstimateAddressInterface;

/**
 * Interface EstimateAddressInterface
 * @api
 */
interface EstimateAddressInterface extends MagentoDataEstimateAddressInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_CITY = 'city';
    const KEY_DISTRICT = 'district';

    /**#@-*/

    /**
     * Get city
     *
     * @return string
     */
    public function getCity();

    /**
     * Set city
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city);


    /**
     * Get district
     *
     * @return string
     */
    public function getDistrict();

    /**
     * Set district
     *
     * @param string $district
     * @return $this
     */
    public function setDistrict($district);
}