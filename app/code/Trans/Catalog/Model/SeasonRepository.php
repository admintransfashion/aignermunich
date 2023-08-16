<?php
/**
 * @category Trans
 * @package  Trans_Catalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

use Trans\Catalog\Api\SeasonRepositoryInterface;
use Trans\Catalog\Api\Data\SeasonInterface;
use Trans\Catalog\Api\Data\SeasonInterfaceFactory;
use Trans\Catalog\Api\Data\SeasonSearchResultsInterfaceFactory;
use Trans\Catalog\Model\ResourceModel\Season as ResourceModel;
use Trans\Catalog\Model\ResourceModel\Season\Collection;
use Trans\Catalog\Model\ResourceModel\Season\CollectionFactory;

/**
 * Class SeasonRepository
 */
class SeasonRepository implements SeasonRepositoryInterface
{
	/**
     * @var array
     */
    protected $instances = [];
    
    /**
     * @var ResourceModel
     */
    protected $resource;
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var Collection
     */
    protected $collection;
    
    /**
     * @var SeasonResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    
    /**
     * @var SeasonInterfaceFactory
     */
    protected $season;
    
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param Collection $collection
     * @param UserStoreResultsInterfaceFactory $searchResultsFactory
     * @param SeasonInterfaceFactory $season
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        SeasonSearchResultsInterfaceFactory $searchResultsFactory,
        SeasonInterfaceFactory $season,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->season = $season;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SeasonInterface $data)
    {
    	/** @var SeasonInterface|\Magento\Framework\Model\AbstractModel $data */
        try {
            $this->resource->save($data);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the data: %1',
                $exception->getMessage()
            ));
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($dataId)
    {
        if (!isset($this->instances[$dataId])) {
            /** @var \Trans\Catalog\Api\Data\SeasonInterface|\Magento\Framework\Model\AbstractModel $season */
            $data = $this->season->create();
            $this->resource->load($data, $dataId);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
            }
            $this->instances[$dataId] = $data;
        }
        return $this->instances[$dataId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($code)
    {
        /** @var \Trans\Catalog\Api\Data\SeasonInterface|\Magento\Framework\Model\AbstractModel $season */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(SeasonInterface::CODE, $code);
        
        $data = $collection->load();
        
        if ($collection->getSize() < 0) {
            throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
        }
        
        $this->instances[$code] = $data;
        
        return $this->instances[$codepp];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Trans\Catalog\Api\Data\SeasonResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Trans\Catalog\Model\ResourceModel\Season\Collection $collection */
        $collection = $this->collectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            // set a default sorting order since this method is used constantly in many
            // different blocks
            $field = 'id';
            $collection->addOrder($field, 'ASC');
        }
        
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var \Trans\Catalog\Api\Data\SeasonInterface[] $season */
        $seasons = [];
        /** @var \Trans\Catalog\Model\Reservation $season */
        foreach ($collection as $season) {
            /** @var \Trans\Catalog\Api\Data\SeasonInterface $seasonDataObject */
            $seasonDataObject = $this->season->create();
            $this->dataObjectHelper->populateWithArray($seasonDataObject, $season->getData(), SprintResponseInterface::class);
            $seasons[] = $seasonDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($seasons);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SeasonInterface $data)
    {
        /** @var \Trans\Catalog\Api\Data\SeasonInterface|\Magento\Framework\Model\AbstractModel $season */
        $dataId = $data->getId();
        try {
            unset($this->instances[$dataId]);
            $this->resource->delete($data);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove data %1', $dataId)
            );
        }
        unset($this->instances[$dataId]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($dataId)
    {
        $data = $this->getById($dataId);
        return $this->delete($data);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }   
}
