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

use Trans\Reservation\Api\ReservationConfigRepositoryInterface;
use Trans\Reservation\Api\Data\ReservationConfigInterface;
use Trans\Reservation\Api\Data\ReservationConfigInterfaceFactory;
use Trans\Reservation\Api\Data\ReservationConfigSearchResultsInterfaceFactory;
use Trans\Reservation\Model\ResourceModel\ReservationConfig as ResourceModel;
use Trans\Reservation\Model\ResourceModel\ReservationConfig\Collection;
use Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory;

/**
 * Class ReservationConfigRepository
 */
class ReservationConfigRepository implements ReservationConfigRepositoryInterface
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
     * @var ReservationConfigSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var ReservationConfigInterfaceFactory
     */
    protected $reservationConfigInterface;

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
     * @param ReservationConfigSearchResultsInterfaceFactory $searchResultsFactory
     * @param ReservationConfigInterfaceFactory $reservationConfigInterface
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param \Trans\Reservation\Helper\Config $configHelper
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        ReservationConfigSearchResultsInterfaceFactory $searchResultsFactory,
        ReservationConfigInterfaceFactory $reservationConfigInterface,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        \Trans\Reservation\Helper\Config $configHelper
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->reservationConfigInterface = $reservationConfigInterface;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ReservationConfigInterface $data)
    {
    	/** @var ReservationConfigInterface|\Magento\Framework\Model\AbstractModel $data */
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
            /** @var \Trans\Reservation\Api\Data\ReservationConfigInterface|\Magento\Framework\Model\AbstractModel $reservation */
            $data = $this->reservationConfigInterface->create();
            $this->resource->load($data, $dataId);

            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data Config doesn\'t exist'));
            }

            $this->instances[$dataId] = $data;
        }
        return $this->instances[$dataId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCategoryIds(array $categoryids, string $config)
    {
        /** @var \Trans\Reservation\Model\RecourceModel\ReservationConfig\CollectionFactory|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('config', array('eq' => $config));

        if(!empty($categoryids)) {
            if(count($categoryids) > 1) {
                $collection->getSelect()->where('category_ids LIKE "%' . $categoryids[0] . '%"');
                foreach($categoryids as $key => $categoryId) {
                    if($key == 0) {
                        continue;
                    }
                    $collection->getSelect()->orWhere('category_ids LIKE "%' . $categoryId . '%"');
                }
            } else {
                $collection->getSelect()->where('category_ids LIKE "%' . $categoryids[0] . '%"');
            }
        }

        $size = $collection->getSize();

        if (!$size) {
            throw new NoSuchEntityException(__('Requested Data Config doesn\'t exist'));
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getByProductSkus(array $productSkus, string $config)
    {
        /** @var \Trans\Reservation\Model\RecourceModel\ReservationConfig\CollectionFactory|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('config', array('eq' => $config));

        if(!empty($productSkus)) {
            if(count($productSkus) > 1) {
                $collection->getSelect()->where('product_skus LIKE "%' . $productSkus[0] . '%"');
                foreach($productSkus as $key => $productSku) {
                    if($key == 0) {
                        continue;
                    }
                    $collection->getSelect()->orWhere('product_skus LIKE "%' . $productSku . '%"');
                }
            } else {
                $collection->getSelect()->where('product_skus LIKE "%' . $productSkus[0] . '%"');
            }
        }

        $size = $collection->getSize();

        if (!$size) {
            throw new NoSuchEntityException(__('Requested Data Config doesn\'t exist'));
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getByValue(int $value, string $config)
    {
        /** @var \Trans\Reservation\Model\RecourceModel\ReservationConfig\CollectionFactory|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(ReservationConfigInterface::CONFIG, array('eq' => $config));
        $collection->addFieldToFilter(ReservationConfigInterface::VALUE, array('eq' => $value));

        $size = $collection->getSize();

        if (!$size) {
            throw new NoSuchEntityException(__('Requested Data Config doesn\'t exist'));
        }

        return $collection->getLastItem();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Trans\Reservation\Model\ResourceModel\ReservationConfig\Collection $collection */
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

        /** @var \Trans\Reservation\Api\Data\ReservationConfigInterface[] $reservation */
        $reservations = [];
        /** @var \Trans\Reservation\Model\Reservation $reservation */
        foreach ($collection as $reservation) {
            /** @var \Trans\Reservation\Api\Data\ReservationConfigInterface $reservationDataObject */
            $reservationDataObject = $this->reservationConfigInterface->create();
            $this->dataObjectHelper->populateWithArray($reservationDataObject, $reservation->getData(), SprintConfigInterface::class);
            $reservations[] = $reservationDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($reservations);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ReservationConfigInterface $data)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationConfigInterface|\Magento\Framework\Model\AbstractModel $reservation */
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
