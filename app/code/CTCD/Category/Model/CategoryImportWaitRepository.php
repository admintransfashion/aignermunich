<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

use CTCD\Category\Api\CategoryImportWaitRepositoryInterface;
use CTCD\Category\Api\Data\CategoryImportWaitInterface;
use CTCD\Category\Api\Data\CategoryImportWaitInterfaceFactory;
use CTCD\Category\Api\Data\CategoryImportWaitSearchResultsInterfaceFactory;
use CTCD\Category\Model\ResourceModel\CategoryImportWait as ResourceModel;
use CTCD\Category\Model\ResourceModel\CategoryImportWait\Collection;
use CTCD\Category\Model\ResourceModel\CategoryImportWait\CollectionFactory;

/**
 * Class CategoryImportWaitRepository
 */
class CategoryImportWaitRepository implements CategoryImportWaitRepositoryInterface
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
     * @var CategoryImportWaitResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CategoryImportWaitInterfaceFactory
     */
    protected $categoryImportWait;

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
     * @param CategoryImportWaitResultsInterfaceFactory $searchResultsFactory
     * @param CategoryImportWaitInterfaceFactory $categoryImportWait
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        CategoryImportWaitSearchResultsInterfaceFactory $searchResultsFactory,
        CategoryImportWaitInterfaceFactory $categoryImportWait,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->categoryImportWait = $categoryImportWait;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CategoryImportWaitInterface $data)
    {
        /** @var CategoryImportWaitInterface|\Magento\Framework\Model\AbstractModel $data */
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
            /** @var \CTCD\Category\Api\Data\CategoryImportWaitInterface|\Magento\Framework\Model\AbstractModel $categoryImportWait */
            $data = $this->categoryImportWait->create();
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
        if (!isset($this->instances[$code])) {
            /** @var \CTCD\Category\Api\Data\CategoryImportWaitInterface|\Magento\Framework\Model\AbstractModel $categoryImportWait */
            $data = $this->categoryImportWait->create();
            $this->resource->load($data, $code, CategoryImportWaitInterface::CODE);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
            }
            $this->instances[$code] = $data;
        }

        return $this->instances[$code];
    }

    /**
     * {@inheritdoc}
     */
    public function getWaitingData()
    {
        $limit = 100;
        /** @var \CTCD\Category\Api\Data\CategoryImportWaitInterface|\Magento\Framework\Model\AbstractModel $categoryImportWait */
        $data = $this->collectionFactory->create();
        $data->setPageSize($limit);
        $data->setOrder('entity_id', 'ASC');

        if (!$data->getSize()) {
            throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \CTCD\Category\Api\Data\CategoryImportWaitResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \CTCD\Category\Model\ResourceModel\Category\Collection $collection */
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
            $field = 'entity_id';
            $collection->addOrder($field, 'ASC');
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var \CTCD\Category\Api\Data\CategoryImportWaitInterface[] $categoryImportWait */
        $categoryImportWaits = [];
        /** @var \CTCD\Category\Model\CategoryImportWait $categoryImportWait */
        foreach ($collection as $categoryImportWait) {
            /** @var \CTCD\Category\Api\Data\CategoryImportWaitInterface $categoryImportWaitDataObject */
            $categoryImportWaitDataObject = $this->categoryImportWait->create();
            $this->dataObjectHelper->populateWithArray($categoryImportWaitDataObject, $categoryImportWait->getData(), SprintResponseInterface::class);
            $categoryImportWaits[] = $categoryImportWaitDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($categoryImportWaits);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CategoryImportWaitInterface $data)
    {
        /** @var \CTCD\Category\Api\Data\CategoryImportWaitInterface|\Magento\Framework\Model\AbstractModel $categoryImportWait */
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
