<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_StoreLocator
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Hadi <ashadi.sejati@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\StoreLocator\Block\Store;

use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Locator extends \Magento\Framework\View\Element\Template
{
	/**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var \CTCD\Reservation\Api\Data\SourceAttributeInterface
     */
    protected $sourceAttribute;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context, 
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        array $data = []
    ) {
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
        
        parent::__construct($context, $data);
    }

    /**
     * retrive all sources
     * 
     * @return Magento\Framework\Api\SearchCriteriaBuilder
     */
    public function getSources()
    {
        $sortOrder = $this->sortOrderBuilder->setField('city')->setDirection('ASC')->create();
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter('source_code', 'default', 'neq')->setSortOrders([$sortOrder])->create();
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
}