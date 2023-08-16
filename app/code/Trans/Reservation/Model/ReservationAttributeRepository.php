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

use Trans\Reservation\Api\ReservationAttributeRepositoryInterface;
use Trans\Reservation\Api\Data\ReservationAttributeInterface;
use Trans\Reservation\Api\Data\ReservationAttributeInterfaceFactory;
use Trans\Reservation\Api\Data\ReservationAttributeSearchResultsInterfaceFactory;
use Trans\Reservation\Model\ResourceModel\ReservationAttribute as ResourceModel;
use Trans\Reservation\Model\ResourceModel\ReservationAttribute\Collection;
use Trans\Reservation\Model\ResourceModel\ReservationAttribute\CollectionFactory;

/**
 * Class ReservationAttributeRepository
 */
class ReservationAttributeRepository implements ReservationAttributeRepositoryInterface
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
     * @var ReservationAttributeResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var ReservationAttributeInterfaceFactory
     */
    protected $reservationAttribute;

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
     * @param ReservationAttributeResultsInterfaceFactory $searchResultsFactory
     * @param ReservationAttributeInterfaceFactory $reservationAttribute
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param StoreManagerInterface $storeManager
     * @param \Trans\Reservation\Helper\Config $configHelper
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        ReservationAttributeSearchResultsInterfaceFactory $searchResultsFactory,
        ReservationAttributeInterfaceFactory $reservationAttribute,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        \Trans\Reservation\Helper\Config $configHelper
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->reservationAttribute = $reservationAttribute;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ReservationAttributeInterface $data)
    {
    	/** @var ReservationAttributeInterface|\Magento\Framework\Model\AbstractModel $data */
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
            /** @var \Trans\Reservation\Api\Data\ReservationAttributeInterface|\Magento\Framework\Model\AbstractModel $reservation */
            $data = $this->reservationAttribute->create();
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
    public function get($attribute, $reservationId)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationAttributeInterface|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(ReservationAttributeInterface::ATTRIBUTE, $attribute);
        $collection->addFieldToFilter(ReservationAttributeInterface::RESERVATION_ID, $reservationId);

        $getLastCollection = $collection->getLastItem();

        try {
            $data = $this->getById($getLastCollection->getId());
        } catch (NoSuchEntityException $e) {
            $data = $this->reservationAttribute->create();
        }

        $this->instances[$reservationId] = $data;

        return $this->instances[$reservationId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByReservationId($dataId)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationAttributeInterface|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(ReservationAttributeInterface::RESERVATION_ID, $dataId);

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
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationAttributeResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Trans\Reservation\Model\ResourceModel\ReservationAttribute\Collection $collection */
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

        /** @var \Trans\Reservation\Api\Data\ReservationAttributeInterface[] $reservation */
        $reservations = [];
        /** @var \Trans\Reservation\Model\Reservation $reservation */
        foreach ($collection as $reservation) {
            /** @var \Trans\Reservation\Api\Data\ReservationAttributeInterface $reservationDataObject */
            $reservationDataObject = $this->reservationAttribute->create();
            $this->dataObjectHelper->populateWithArray($reservationDataObject, $reservation->getData(), SprintResponseInterface::class);
            $reservations[] = $reservationDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($reservations);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ReservationAttributeInterface $data)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationAttributeInterface|\Magento\Framework\Model\AbstractModel $reservation */
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

    /**
     * {@inheritdoc}
     */
    public function generateReservationNumber()
    {
        $attribute = ReservationAttributeInterface::LAST_RESERVATION_NUMBER_ATTR;
        $reservationId = ReservationAttributeInterface::ATTR_GLOBAL;

        $model  = $this->get($attribute, $reservationId);

        $lastQueue = $model->getData('value');
        $numberDigit = $this->configHelper->getReservationNumberDigit();

        if (!$lastQueue) {
            $new = 1;
        } else {
            $new = $lastQueue + 1;
        }

        $new = str_pad($new, $numberDigit, "0", STR_PAD_LEFT);

        $model->setReservationId($reservationId);
        $model->setAttribute($attribute);
        $model->setValue($new);

        $transactionSave = $this->save($model);

        return $new;
    }
}
