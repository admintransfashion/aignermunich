<?php
/**
 * @category Trans
 * @package  Trans_CatalogMultisource
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CatalogMultisource\Model\Source;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

use Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface;
use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface;
use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory;
use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistorySearchResultsInterfaceFactory;
use Trans\CatalogMultisource\Model\ResourceModel\SourceItemUpdateHistory as ResourceModel;
use Trans\CatalogMultisource\Model\ResourceModel\SourceItemUpdateHistory\Collection;
use Trans\CatalogMultisource\Model\ResourceModel\SourceItemUpdateHistory\CollectionFactory;

/**
 * Class SourceItemUpdateHistoryRepository
 */
class SourceItemUpdateHistoryRepository implements SourceItemUpdateHistoryRepositoryInterface
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
     * @var SourceItemUpdateHistorySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    
    /**
     * @var SourceItemUpdateHistoryInterfaceFactory
     */
    protected $historyInterface;
    
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
     * @param SourceItemUpdateHistorySearchResultsInterfaceFactory $searchResultsFactory
     * @param SourceItemUpdateHistoryInterfaceFactory $historyInterface
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        SourceItemUpdateHistorySearchResultsInterfaceFactory $searchResultsFactory,
        SourceItemUpdateHistoryInterfaceFactory $historyInterface,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->historyInterface = $historyInterface;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SourceItemUpdateHistoryInterface $data)
    {
    	/** @var historyInterface|\Magento\Framework\Model\AbstractModel $data */
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
            /** @var \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface|\Magento\Framework\Model\AbstractModel $sourceItemUpdateHistory */
            $data = $this->historyInterface->create();
            $this->resource->load($data, $dataId);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data Response doesn\'t exist'));
            }
            $this->instances[$dataId] = $data;
        }
        return $this->instances[$dataId];
    }

    /**
     * {@inheritdoc}
     */
    public function getBySku($sku)
    {
        if (!isset($this->instances[$sku])) {
            /** @var \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface|\Magento\Framework\Model\AbstractModel $sourceItemUpdateHistory */
            $sourceItemUpdateHistory = $this->historyInterface->create();
            $this->resource->load($sourceItemUpdateHistory, $sku, SourceItemUpdateHistoryInterface::SKU);

            if (count($sourceItemUpdateHistory) === 0) {
                throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
            }
            
            $this->instances[$sku] = $sourceItemUpdateHistory;
        }

        return $this->instances[$sku];
    }

    /**
     * {@inheritdoc}
     */
    public function getBySourceCode($code)
    {
        if (!isset($this->instances[$code])) {
            /** @var \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface|\Magento\Framework\Model\AbstractModel $sourceItemUpdateHistory */
            $sourceItemUpdateHistory = $this->historyInterface->create();
            $this->resource->load($sourceItemUpdateHistory, $code, SourceItemUpdateHistoryInterface::SOURCE_CODE);

            if (count($sourceItemUpdateHistory) === 0) {
                throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
            }
            
            $this->instances[$code] = $sourceItemUpdateHistory;
        }

        return $this->instances[$code];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistorySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Trans\CatalogMultisource\Model\ResourceModel\SourceItemUpdateHistory\Collection $collection */
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

        /** @var \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface[] $sourceItemUpdateHistory */
        $sourceItemUpdateHistorys = [];
        /** @var \Trans\CatalogMultisource\Model\SourceItemUpdateHistory $sourceItemUpdateHistory */
        foreach ($collection as $sourceItemUpdateHistory) {
            /** @var \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface $sourceItemUpdateHistoryDataObject */
            $sourceItemUpdateHistoryDataObject = $this->historyInterface->create();
            $this->dataObjectHelper->populateWithArray($sourceItemUpdateHistoryDataObject, $sourceItemUpdateHistory->getData(), SprintResponseInterface::class);
            $sourceItemUpdateHistorys[] = $sourceItemUpdateHistoryDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($sourceItemUpdateHistorys);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SourceItemUpdateHistoryInterface $sourceItemUpdateHistory)
    {
        /** @var \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface|\Magento\Framework\Model\AbstractModel $sourceItemUpdateHistory */
        $sourceItemUpdateHistoryId = $sourceItemUpdateHistory->getId();
        try {
            unset($this->instances[$sourceItemUpdateHistoryId]);
            $this->resource->delete($sourceItemUpdateHistory);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Sprint Response %1', $sourceItemUpdateHistoryId)
            );
        }
        unset($this->instances[$sourceItemUpdateHistoryId]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($sourceItemUpdateHistoryId)
    {
        $sourceItemUpdateHistory = $this->getById($sourceItemUpdateHistoryId);
        return $this->delete($sourceItemUpdateHistory);
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