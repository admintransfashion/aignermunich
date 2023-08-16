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

use CTCD\Category\Api\Data\CategoryImportInterface;
use CTCD\Category\Api\CategoryImportDataRepositoryInterface;
use CTCD\Category\Api\Data\CategoryImportDataInterface;
use CTCD\Category\Api\Data\CategoryImportDataInterfaceFactory;
use CTCD\Category\Api\Data\CategoryImportDataSearchResultsInterfaceFactory;
use CTCD\Category\Model\ResourceModel\CategoryImportData as ResourceModel;
use CTCD\Category\Model\ResourceModel\CategoryImportData\Collection;
use CTCD\Category\Model\ResourceModel\CategoryImportData\CollectionFactory;

/**
 * Class CategoryImportDataRepository
 */
class CategoryImportDataRepository implements CategoryImportDataRepositoryInterface
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
     * @var CategoryImportDataResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CategoryImportDataInterfaceFactory
     */
    protected $categoryImportData;

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
     * @param CategoryImportDataResultsInterfaceFactory $searchResultsFactory
     * @param CategoryImportDataInterfaceFactory $categoryImportData
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        CategoryImportDataSearchResultsInterfaceFactory $searchResultsFactory,
        CategoryImportDataInterfaceFactory $categoryImportData,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->categoryImportData = $categoryImportData;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CategoryImportDataInterface $data)
    {
        /** @var CategoryImportDataInterface|\Magento\Framework\Model\AbstractModel $data */
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
            /** @var \CTCD\Category\Api\Data\CategoryImportDataInterface|\Magento\Framework\Model\AbstractModel $categoryImportData */
            $data = $this->categoryImportData->create();
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
    public function getByImportId($importId)
    {
        if (!isset($this->instances[$importId])) {
            /** @var \CTCD\Category\Api\Data\CategoryImportDataInterface|\Magento\Framework\Model\AbstractModel $categoryImportData */
            $data = $this->categoryImportData->create();
            $this->resource->load($data, $importId, CategoryImportInterface::IMPORT_ID);

            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
            }

            $this->instances[$importId] = $data;
        }

        return $this->instances[$importId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \CTCD\Category\Api\Data\CategoryImportDataResultsInterface $searchResults */
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

        /** @var \CTCD\Category\Api\Data\CategoryImportDataInterface[] $categoryImportData */
        $categoryImportDatas = [];
        /** @var \CTCD\Category\Model\CategoryImportData $categoryImportData */
        foreach ($collection as $categoryImportData) {
            /** @var \CTCD\Category\Api\Data\CategoryImportDataInterface $categoryImportDataDataObject */
            $categoryImportDataDataObject = $this->categoryImportData->create();
            $this->dataObjectHelper->populateWithArray($categoryImportDataDataObject, $categoryImportData->getData(), SprintResponseInterface::class);
            $categoryImportDatas[] = $categoryImportDataDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($categoryImportDatas);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CategoryImportDataInterface $data)
    {
        /** @var \CTCD\Category\Api\Data\CategoryImportDataInterface|\Magento\Framework\Model\AbstractModel $categoryImportData */
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
