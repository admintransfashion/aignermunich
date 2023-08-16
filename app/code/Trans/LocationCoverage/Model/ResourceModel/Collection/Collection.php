<?php
/**
 * Copyright © 2018 EaDesign by Eco Active S.R.L. All rights reserved.
 * See LICENSE for license details.
 */

namespace Trans\LocationCoverage\Model\ResourceModel\Collection;

use Trans\LocationCoverage\Model\City as CityModel;
use Trans\LocationCoverage\Model\District as DistrictModel;
use Trans\LocationCoverage\Model\ResourceModel\City  as CityResourceModel;
use Trans\LocationCoverage\Model\ResourceModel\District  as DistrictResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $_idCity = 'entity_id';
    protected $_idDistrict = 'district_id';

    /**
     * Init resource model
     * @return void
     */
    public function _construct()
    {

        $this->_init(
            CityModel::class,
            CityResourceModel::class,
            DistrictModel::class,
            DistrictResourceModel::class
        );
        $this->_map['city']['entity_id'] = 'main_table.entity_id';
        $this->_map['district']['district_id'] = 'main_table.district_id';
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        return $this;
    }
}