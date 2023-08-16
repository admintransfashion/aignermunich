<?php
/**
 * Copyright © 2018 EaDesign by Eco Active S.R.L. All rights reserved.
 * See LICENSE for license details.
 */

namespace Trans\LocationCoverage\Model\ResourceModel\City;

use Trans\LocationCoverage\Model\City as CityModel;
use Trans\LocationCoverage\Model\District as DistrictModel;
use Trans\LocationCoverage\Model\ResourceModel\City  as CityResourceModel;
use Trans\LocationCoverage\Model\ResourceModel\District  as DistrictResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_idCity = 'entity_id';
    
    /**
     * Init resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CityModel::class, CityResourceModel::class);
    }
}