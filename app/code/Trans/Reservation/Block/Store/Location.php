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

namespace Trans\Reservation\Block\Store;

use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

/**
 * Class Location
 */
class Location extends \Magento\Framework\View\Element\Template
{
	/**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var \Trans\Reservation\Api\Data\SourceAttributeInterface
     */
    private $sourceAttribute;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute,
        array $data = []
    ) {
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sourceAttribute = $sourceAttribute;
        parent::__construct($context, $data);
    }

    /**
     * retrive all sources
     *
     * @return Magento\Framework\Api\SearchCriteriaBuilder
     */
    public function getSources()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter('source_code', 'default', 'neq')->create();
        $sources = $this->sourceRepository->getList($searchCriteria)->getItems();

        return $sources;
    }

    /**
     * get source address
     *
     * @param \Magento\InventoryApi\Api\Data\SourceInterface
     * @return string
     */
    public function getSourceAddress($source)
    {
        $data[] = $source->getStreet() ? $source->getStreet() : '';
        $data[] = $source->getRegion() ? $source->getRegion() : '';
        $data[] = $source->getCity() ? $source->getCity() : '';
        $data[] = $source->getPostcode() ? $source->getPostcode() : '';

        $string = implode(', ', $data);

        return $string;
    }

    /**
     * get source open hour
     *
     * @param \Magento\InventoryApi\Api\Data\SourceInterface
     * @return string
     */
    public function getStoreOpenHour($source)
    {
        return $this->sourceAttribute->getOpenTime($source->getSourceCode());
    }

    /**
     * get source close hour
     *
     * @param \Magento\InventoryApi\Api\Data\SourceInterface
     * @return string
     */
    public function getStoreCloseHour($source)
    {
        return $this->sourceAttribute->getCloseTime($source->getSourceCode());
    }

    /**
     * get source hours
     *
     * @param \Magento\InventoryApi\Api\Data\SourceInterface
     * @return string
     */
    public function getSourceHours($source)
    {
        return $this->getStoreOpenHour($source) . ' - ' . $this->getStoreCloseHour($source);
    }
}
