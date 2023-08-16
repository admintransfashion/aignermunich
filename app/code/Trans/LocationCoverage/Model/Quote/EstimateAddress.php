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

namespace Trans\LocationCoverage\Model\Quote;

use Trans\LocationCoverage\Api\Quote\Data\EstimateAddressInterface;
use Magento\Quote\Model\EstimateAddress as MagentoEstimateAddress;

/**
 * Class EstimateAddress
 * @package Trans\LocationCoverage\Model\Qoute
 */
class EstimateAddress extends MagentoEstimateAddress implements EstimateAddressInterface
{
    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->getData(self::KEY_CITY);
    }

    /**
     * Set city
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        return $this->setData(self::KEY_CITY, $city);
    }

    /**
     * Get district
     *
     * @return string
     */
    public function getDistrict()
    {
        return $this->getData(self::KEY_DISTRICT);
    }

    /**
     * Set district
     *
     * @param string $district
     * @return $this
     */
    public function setDistrict($district)
    {
        return $this->setData(self::KEY_DISTRICT, $district);
    }
}