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

namespace Trans\LocationCoverage\Block\Checkout;

use Trans\LocationCoverage\Model\CityRepository;
use Trans\LocationCoverage\Model\DistrictRepository;
use Trans\LocationCoverage\Model\City;
use Trans\LocationCoverage\Model\District;
use Trans\LocationCoverage\Model\ResourceModel\Collection\Collection;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Directory\Helper\Data;

/**
 * Class Cities
 * @package Trans\LocationCoverage\Block\Checkout
 */
class Cities extends Template
{
    /**
     * @var $helperData 
     */
    private $helperData;

    /**
     * @var $cityRepository 
     */
    private $cityRepository;

    /**
     * @var $districtRepository 
     */
    private $districtRepository;

    /**
     * @var $cityModel 
     */
    private $cityModel;

    /**
     * @var $districtModel 
     */
    private $districtModel;

    /**
     * @var $collection 
     */
    private $collection;

    /**
     * @var $searchCriteria 
     */
    private $searchCriteria;

    /**
     * @param Template\Context $context
     * @param Data $helperData
     * @param City $cityModel
     * @param District $districtModel
     * @param Collection $collection
     * @param CityRepository $cityRepository
     * @param DistrictRepository $districtRepository
     * @param SearchCriteriaBuilder $searchCriteria
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helperData,
        City $cityModel,
        District $districtModel,
        Collection $collection,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        SearchCriteriaBuilder $searchCriteria,
        array $data = []
    ) {
        $this->cityModel = $cityModel;
        $this->districtModel = $districtModel;
        $this->searchCriteria = $searchCriteria;
        $this->collection = $collection;
        $this->helperData   = $helperData;
        $this->cityRepository = $cityRepository;
        $this->districtRepository = $districtRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get data City from Region
     *
     */
    public function citiesJson()
    {
        $countriesJson = $this->helperData->getRegionJson();
        $countriesArray = json_decode($countriesJson, true);

        $searchCriteriaBuilder = $this->searchCriteria;
        $searchCriteria = $searchCriteriaBuilder->create();

        $citiesList = $this->cityRepository->getList($searchCriteria);
        $items = $citiesList->getItems();

        /** @var City $item */
        foreach ($items as $item) {
            $citiesData[$item->getEntityId()] = $item;
        }

        $countriesArrayUpdated = [];
        foreach ($countriesArray as $key => $country) {
            if ($key == 'config') {
                $countriesArrayUpdated[$key] = $country;
            }

            $regions = [];
            foreach ($country as $regionId => $region) {
                foreach ($citiesData as $cityId => $cityData) {
                    $entityId = $cityData->getRegionId();
                    if ($entityId == $regionId) {
                        $id       = $cityData->getId();
                        $cityName = $cityData->getCityName();
                        $region['cities'][$id] = [
                            'name' => $cityName,
                            'id' => $cityId
                        ];
                    }
                }

                $regions[$regionId] = $region;
            }
            $countriesArrayUpdated[$key] = $regions;
        }

        $countriesJsonUpdated = json_encode($countriesArrayUpdated);

        return $countriesJsonUpdated;
    }
}