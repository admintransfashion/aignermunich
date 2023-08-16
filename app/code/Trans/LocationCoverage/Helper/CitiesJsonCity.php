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

namespace Trans\LocationCoverage\Helper;

use Trans\LocationCoverage\Model\CityRepository;
use Trans\LocationCoverage\Model\City;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Directory\Helper\Data;

/**
 * Class CitiesJsonCity
 * @package Trans\LocationCoverage\Helper
 */
class CitiesJsonCity extends Data
{
    /**
     * @var $cityRepository 
     */
    private $cityRepository;

    /**
     * @var $searchCriteria 
     */
    private $searchCriteria;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regCollectionFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param CityRepository $cityRepository
     * @param SearchCriteriaBuilder $searchCriteria
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regCollectionFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        CityRepository $cityRepository,
        SearchCriteriaBuilder $searchCriteria
    ) {
        $this->searchCriteria = $searchCriteria;
        $this->cityRepository = $cityRepository;
        parent::__construct(
            $context,
            $configCacheType,
            $countryCollection,
            $regCollectionFactory,
            $jsonHelper,
            $storeManager,
            $currencyFactory
        );
    }
    
    /**
     * Retrieve regions data
     *
     * @return array
     */
    public function getRegionData()
    {
        $countryIds = [];
        foreach ($this->getCountryCollection() as $country) {
            $countryIds[] = $country->getCountryId();
        }
        $collection = $this->_regCollectionFactory->create();
        $collection->addCountryFilter($countryIds)->load();
        $regions = [
            'config' => [
                'show_all_regions' => $this->isShowNonRequiredState(),
                'regions_required' => $this->getCountriesWithStatesRequired(),
            ],
        ];

        $searchCriteriaBuilder = $this->searchCriteria;
        $searchCriteria = $searchCriteriaBuilder->create();

        $citiesList = $this->cityRepository->getList($searchCriteria);
        $items = $citiesList->getItems();

        /** @var City $item */
        foreach ($items as $item) {
            $citiesData[$item->getEntityId()] = $item;
        }

        foreach ($collection as $region) {
            /** @var $region \Magento\Directory\Model\Region */
            if (!$region->getRegionId()) {
                continue;
            }

            $cities = [];
            foreach ($citiesData as $cityId => $cityData) {
                $entityId = $cityData->getRegionId();
                $regionId = $region->getId();
                if ($entityId == $regionId) {
                    $id       = $cityData->getId();
                    $cityName = $cityData->getCityName();
                    $cities[$id] = [
                        'name' => $cityName,
                        'id' => $cityId
                    ];
                }
            }

            $regions[$region->getCountryId()][$region->getRegionId()] = [
                'code' => $region->getCode(),
                'name' => (string)__($region->getName()),
                'cities' => $cities
            ];
        }
        return $regions;
    }

}