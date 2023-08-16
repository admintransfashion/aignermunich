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

use CTCD\Category\Api\CategoryImportMapRepositoryInterface;
use CTCD\Category\Api\Data\CategoryImportMapInterface;
use CTCD\Category\Api\Data\CategoryImportMapInterfaceFactory;
use CTCD\Category\Api\Data\CategoryImportMapSearchResultsInterfaceFactory;
use CTCD\Category\Model\ResourceModel\CategoryImportMap as ResourceModel;
use CTCD\Category\Model\ResourceModel\CategoryImportMap\Collection;
use CTCD\Category\Model\ResourceModel\CategoryImportMap\CollectionFactory;

/**
 * Class CategoryImportMapRepository
 */
class CategoryImportMapRepository implements CategoryImportMapRepositoryInterface
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

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
     * @var CategoryImportMapResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CategoryImportMapInterfaceFactory
     */
    protected $categoryImportMap;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param Collection $collection
     * @param CategoryImportMapResultsInterfaceFactory $searchResultsFactory
     * @param CategoryImportMapInterfaceFactory $categoryImportMap
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        CategoryImportMapSearchResultsInterfaceFactory $searchResultsFactory,
        CategoryImportMapInterfaceFactory $categoryImportMap,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->categoryImportMap = $categoryImportMap;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CategoryImportMapInterface $data)
    {
        /** @var CategoryImportMapInterface|\Magento\Framework\Model\AbstractModel $data */
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
    public function getByCode($code)
    {
        /** @var \CTCD\Category\Api\Data\CategoryImportMapInterface|\Magento\Framework\Model\AbstractModel $categoryImportMap */
        $data = $this->categoryImportMap->create();
        $this->resource->load($data, $code, CategoryImportMapInterface::CATEGORY_CODE);
        if (!$data->getId()) {
            throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryByCode($codeData)
    {
        if (!isset($this->instances[$codeData])) {
            /** @var \CTCD\Category\Api\Data\CategoryInterface|\Magento\Framework\Model\AbstractModel $categoryImportMap */
            $data = $this->getByCode($codeData);

            try {
                $category = $this->categoryRepository->get($data->getCategoryId());
            } catch (NoSuchEntityException $e) {
                throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
            }

            if (!$category->getId()) {
                throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
            }
            $this->instances[$codeData] = $category;
        }

        return $this->instances[$codeData];
    }

    /**
     * {@inheritdoc}
     */
    public function getByOfflineMap($offlineMap)
    {
        if (!isset($this->instances[$offlineMap])) {
            /** @var \CTCD\Category\Api\Data\CategoryInterface|\Magento\Framework\Model\AbstractModel $categoryImportMap */
            $data = $this->collectionFactory->create();
            $data->addFieldToFilter(CategoryImportMapInterface::OFFLINE_ID, array('like' => '%"' . $offlineMap . '"%'));

            if (!$data->getSize()) {
                throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
            }
            $this->instances[$offlineMap] = $data;
        }

        return $this->instances[$offlineMap];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCategoryId($categoryId)
    {
        if (!isset($this->instances[$categoryId])) {
            /** @var \CTCD\Category\Api\Data\CategoryImportMapInterface|\Magento\Framework\Model\AbstractModel $categoryImportMap */
            $data = $this->categoryImportMap->create();
            $this->resource->load($data, $categoryId, CategoryImportMapInterface::CATEGORY_ID);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
            }
            $this->instances[$categoryId] = $data;
        }

        return $this->instances[$categoryId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \CTCD\Category\Api\Data\CategoryImportMapResultsInterface $searchResults */
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

        /** @var \CTCD\Category\Api\Data\CategoryImportMapInterface[] $categoryImportMap */
        $categoryImportMaps = [];
        /** @var \CTCD\Category\Model\CategoryImportMap $categoryImportMap */
        foreach ($collection as $categoryImportMap) {
            /** @var \CTCD\Category\Api\Data\CategoryImportMapInterface $categoryImportMapDataObject */
            $categoryImportMapDataObject = $this->categoryImportMap->create();
            $this->dataObjectHelper->populateWithArray($categoryImportMapDataObject, $categoryImportMap->getData(), SprintResponseInterface::class);
            $categoryImportMaps[] = $categoryImportMapDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($categoryImportMaps);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CategoryImportMapInterface $data)
    {
        /** @var \CTCD\Category\Api\Data\CategoryImportMapInterface|\Magento\Framework\Model\AbstractModel $categoryImportMap */
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
