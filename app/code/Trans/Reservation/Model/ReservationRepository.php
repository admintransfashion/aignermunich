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

use Trans\Reservation\Api\ReservationRepositoryInterface;
use Trans\Reservation\Api\ReservationItemRepositoryInterface;
use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationInterfaceFactory;
use Trans\Reservation\Api\Data\ReservationItemInterfaceFactory;
use Trans\Reservation\Api\Data\ReservationSearchResultsInterfaceFactory;
use Trans\Reservation\Model\ResourceModel\Reservation as ResourceModel;
use Trans\Reservation\Model\ResourceModel\Reservation\Collection;
use Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory;

/**
 * Class ReservationRepository
 */
class ReservationRepository implements ReservationRepositoryInterface
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
     * @var ReservationSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var ReservationInterfaceFactory
     */
    protected $reservationInterface;

    /**
     * @var ReservationItemInterfaceFactory
     */
    protected $reservationItem;

    /**
     * @var ReservationItemRepositoryInterface
     */
    protected $reservationItemRepository;

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
     * @param ReservationSearchResultsInterfaceFactory $searchResultsFactory
     * @param ReservationInterfaceFactory $reservationInterface
     * @param ReservationItemInterfaceFactory $reservationItemInterface
     * @param ReservationItemRepositoryInterface $reservationItemRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param \Trans\Reservation\Helper\Config $configHelper
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        ReservationSearchResultsInterfaceFactory $searchResultsFactory,
        ReservationInterfaceFactory $reservationInterface,
        ReservationItemInterfaceFactory $reservationItemInterface,
        ReservationItemRepositoryInterface $reservationItemRepository,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        \Trans\Reservation\Helper\Config $configHelper
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->reservationInterface = $reservationInterface;
        $this->reservationItem = $reservationItemInterface;
        $this->reservationItemRepository = $reservationItemRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ReservationInterface $data)
    {
    	/** @var reservationInterface|\Magento\Framework\Model\AbstractModel $data */
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
            /** @var \Trans\Reservation\Api\Data\ReservationInterface|\Magento\Framework\Model\AbstractModel $reservation */
            $data = $this->reservationInterface->create();
            $this->resource->load($data, $dataId);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data Reservation Response doesn\'t exist'));
            }
            $this->instances[$dataId] = $data;
        }
        return $this->instances[$dataId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCustomerId($customerId)
    {
        if (!isset($this->instances[$customerId])) {
            /** @var \Trans\Reservation\Api\Data\ReservationInterface|\Magento\Framework\Model\AbstractModel $reservation */
            $data = $this->reservationInterface->create();
            $this->resource->load($data, $customerId, ReservationInterface::CUSTOMER_ID);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data Response doesn\'t exist'));
            }
            $this->instances[$customerId] = $data;
        }
        return $this->instances[$customerId];
    }

    /**
     * {@inheritdoc}
     */
    public function getBySourceCode($code)
    {
        if (!isset($this->instances[$code])) {
            /** @var \Trans\Reservation\Api\Data\ReservationInterface|\Magento\Framework\Model\AbstractModel $reservation */
            $reservation = $this->reservationInterface->create();
            $this->resource->load($reservation, $code, ReservationInterface::SOURCE_CODE);

            if (count($reservation) === 0) {
                throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
            }

            $this->instances[$code] = $reservation;
        }

        return $this->instances[$code];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Trans\Reservation\Model\ResourceModel\Reservation\Collection $collection */
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

        /** @var \Trans\Reservation\Api\Data\ReservationInterface[] $reservation */
        $reservations = [];
        /** @var \Trans\Reservation\Model\Reservation $reservation */
        foreach ($collection as $reservation) {
            /** @var \Trans\Reservation\Api\Data\ReservationInterface $reservationDataObject */
            $reservationDataObject = $this->reservationInterface->create();
            $this->dataObjectHelper->populateWithArray($reservationDataObject, $reservation->getData(), SprintResponseInterface::class);
            $reservations[] = $reservationDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($reservations);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ReservationInterface $reservation)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationInterface|\Magento\Framework\Model\AbstractModel $reservation */
        $reservationId = $reservation->getId();
        try {
            unset($this->instances[$reservationId]);
            $this->resource->delete($reservation);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove data %1', $reservationId)
            );
        }
        unset($this->instances[$reservationId]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($reservationId)
    {
        $reservation = $this->getById($reservationId);
        return $this->delete($reservation);
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
