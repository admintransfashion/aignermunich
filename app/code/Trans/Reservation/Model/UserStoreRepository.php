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

namespace Trans\Reservation\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

use Trans\Reservation\Api\UserStoreRepositoryInterface;
use Trans\Reservation\Api\Data\UserStoreInterface;
use Trans\Reservation\Api\Data\UserStoreInterfaceFactory;
use Trans\Reservation\Api\Data\UserStoreSearchResultsInterfaceFactory;
use Trans\Reservation\Model\ResourceModel\UserStore as ResourceModel;
use Trans\Reservation\Model\ResourceModel\UserStore\Collection;
use Trans\Reservation\Model\ResourceModel\UserStore\CollectionFactory;

/**
 * Class UserStoreRepository
 */
class UserStoreRepository implements UserStoreRepositoryInterface
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
     * @var UserStoreResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var UserStoreInterfaceFactory
     */
    protected $userStore;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var \Trans\Reservation\Helper\Config
     */
    protected $configHelper;

    /**
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param Collection $collection
     * @param UserStoreResultsInterfaceFactory $searchResultsFactory
     * @param UserStoreInterfaceFactory $userStore
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param StoreManagerInterface $storeManager
     * @param \Trans\Reservation\Helper\Config $configHelper
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        UserStoreSearchResultsInterfaceFactory $searchResultsFactory,
        UserStoreInterfaceFactory $userStore,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        \Trans\Reservation\Helper\Config $configHelper
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->userStore = $userStore;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(UserStoreInterface $data)
    {
    	/** @var UserStoreInterface|\Magento\Framework\Model\AbstractModel $data */
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
            /** @var \Trans\Reservation\Api\Data\UserStoreInterface|\Magento\Framework\Model\AbstractModel $userStore */
            $data = $this->userStore->create();
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
    public function getByUserId($dataId)
    {
        /** @var \Trans\Reservation\Api\Data\UserStoreInterface|\Magento\Framework\Model\AbstractModel $userStore */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(UserStoreInterface::USER_ID, $dataId);

        $data = $collection->load();

        if ($collection->getSize() < 0) {
            throw new NoSuchEntityException(__('Requested Data Response doesn\'t exist'));
        }

        $this->instances[$dataId] = $data;

        return $this->instances[$dataId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByStoreCode($code)
    {
        /** @var \Trans\Reservation\Api\Data\UserStoreInterface|\Magento\Framework\Model\AbstractModel $userStore */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(UserStoreInterface::STORE_CODE, $code);

        $data = $collection->load();

        if ($collection->getSize() < 0) {
            throw new NoSuchEntityException(__('Requested Data Response doesn\'t exist'));
        }

        $this->instances[$code] = $data;

        return $this->instances[$code];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Trans\Reservation\Api\Data\UserStoreResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Trans\Reservation\Model\ResourceModel\UserStore\Collection $collection */
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

        /** @var \Trans\Reservation\Api\Data\UserStoreInterface[] $userStore */
        $userStores = [];
        /** @var \Trans\Reservation\Model\Reservation $userStore */
        foreach ($collection as $userStore) {
            /** @var \Trans\Reservation\Api\Data\UserStoreInterface $userStoreDataObject */
            $userStoreDataObject = $this->userStore->create();
            $this->dataObjectHelper->populateWithArray($userStoreDataObject, $userStore->getData(), SprintResponseInterface::class);
            $userStores[] = $userStoreDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($userStores);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(UserStoreInterface $data)
    {
        /** @var \Trans\Reservation\Api\Data\UserStoreInterface|\Magento\Framework\Model\AbstractModel $userStore */
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
