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

use Trans\Reservation\Api\ReservationItemRepositoryInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;
use Trans\Reservation\Api\Data\ReservationItemInterfaceFactory;
use Trans\Reservation\Api\Data\ReservationItemSearchResultsInterfaceFactory;
use Trans\Reservation\Model\ResourceModel\ReservationItem as ResourceModel;
use Trans\Reservation\Model\ResourceModel\ReservationItem\Collection;
use Trans\Reservation\Model\ResourceModel\ReservationItem\CollectionFactory;

/**
 * Class ReservationItemRepository
 */
class ReservationItemRepository implements ReservationItemRepositoryInterface
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
     * @var ReservationItemResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var ReservationItemInterfaceFactory
     */
    protected $reservationItem;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Trans\Reservation\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Trans\CatalogMultisource\Helper\SourceItem
     */
    protected $sourceItemHelper;

    /**
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param Collection $collection
     * @param ReservationItemResultsInterfaceFactory $searchResultsFactory
     * @param ReservationItemInterfaceFactory $reservationItem
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepository $productRepository
     * @param \Trans\Reservation\Helper\Config $configHelper
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        ReservationItemSearchResultsInterfaceFactory $searchResultsFactory,
        ReservationItemInterfaceFactory $reservationItem,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Trans\Reservation\Helper\Config $configHelper,
        \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->reservationItem = $reservationItem;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->configHelper = $configHelper;
        $this->sourceItemHelper = $sourceItemHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ReservationItemInterface $data)
    {
    	/** @var ReservationItemInterface|\Magento\Framework\Model\AbstractModel $data */
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
        $this->instances[$dataId] = null;
        if (!isset($this->instances[$dataId])) {
            /** @var \Trans\Reservation\Api\Data\ReservationItemInterface|\Magento\Framework\Model\AbstractModel $reservation */
            $data = $this->reservationItem->create();
            $this->resource->load($data, $dataId, 'id');
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data Reservation Item doesn\'t exist'));
            }
            $this->instances[$dataId] = $data;
        }
        return $this->instances[$dataId];
    }

    /**
     * {@inheritdoc}
     */
    public function get($reservationId, $productId)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationItemInterface|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(ReservationItemInterface::RESERVATION_ID, $reservationId);
        $collection->addFieldToFilter(ReservationItemInterface::PRODUCT_ID, $productId);

        $getLastCollection = $collection->getLastItem();

        try {
            $data = $this->getById($getLastCollection->getId());
        } catch (NoSuchEntityException $e) {
            $data = $this->reservationItem->create();
        }

        $this->instances[$reservationId] = $data;

        return $this->instances[$reservationId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByReservationId($dataId)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationItemInterface|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(ReservationItemInterface::RESERVATION_ID, $dataId);

        $data = $collection->load();

        if ($collection->getSize() < 0) {
            throw new NoSuchEntityException(__('Requested Data Reservation Item doesn\'t exist'));
        }

        $this->instances[$dataId] = $data;

        return $this->instances[$dataId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByReference($orderId)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationItemInterface|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(ReservationItemInterface::REFERENCE_NUMBER, $orderId);

        $data = $collection->load();

        if ($collection->getSize() < 0) {
            throw new NoSuchEntityException(__('Requested Data Reservation Item doesn\'t exist'));
        }

        $this->instances[$orderId] = $data;

        return $this->instances[$orderId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByReferenceItemIds(string $orderId, array $items)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationItemInterface|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(ReservationItemInterface::REFERENCE_NUMBER, $orderId);
        $collection->addFieldToFilter(ReservationItemInterface::PRODUCT_ID, array('in' => $items));

        $data = $collection->load();

        if ($collection->getSize() < 0) {
            throw new NoSuchEntityException(__('Requested Data Reservation Item doesn\'t exist'));
        }

        $this->instances[$orderId] = $data;

        return $this->instances[$orderId];
    }

    /**
     * {@inheritdoc}
     */
    public function getBySku($sku)
    {
        if (!isset($this->instances[$sku])) {
            /** @var \Trans\Reservation\Api\Data\ReservationItemInterface|\Magento\Framework\Model\AbstractModel $reservation */
            $reservation = $this->reservationItem->create();
            $this->resource->load($reservation, $sku, ReservationItemInterface::SKU);

            if (count($reservation) === 0) {
                throw new NoSuchEntityException(__('Requested data reservation item doesn\'t exist'));
            }

            $this->instances[$sku] = $reservation;
        }

        return $this->instances[$sku];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationItemResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Trans\Reservation\Model\ResourceModel\ReservationItem\Collection $collection */
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

        /** @var \Trans\Reservation\Api\Data\ReservationItemInterface[] $reservation */
        $reservations = [];
        /** @var \Trans\Reservation\Model\Reservation $reservation */
        foreach ($collection as $reservation) {
            /** @var \Trans\Reservation\Api\Data\ReservationItemInterface $reservationDataObject */
            $reservationDataObject = $this->reservationItem->create();
            $this->dataObjectHelper->populateWithArray($reservationDataObject, $reservation->getData(), SprintResponseInterface::class);
            $reservations[] = $reservationDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($reservations);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ReservationItemInterface $reservation)
    {
        /** @var \Trans\Reservation\Api\Data\ReservationItemInterface|\Magento\Framework\Model\AbstractModel $reservation */
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
    public function deleteById($reservationItemId)
    {
        $reservationItem = $this->getById($reservationItemId);
        return $this->delete($reservationItem);
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
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function isItemAddable($reservationId, $sourceCode, $productId = null, $qty = null)
    {
        $maxQty = $this->configHelper->getMaxQty();
        $buffer = $this->configHelper->getProductBuffer();

        if($this->configHelper->isQtyForItem()) {
            try {
                $item = $this->get($reservationId, $productId);
                if(($item->getQty() + $qty) >= $maxQty) {
                    return false;
                }
            } catch (NoSuchEntityException $e) {

            }
        } else {
            $sumqty = 0;
            try {
                $reservation = $this->getByReservationId($reservationId);
                $count = count($reservation);

                if($count == $maxQty) {
                    return false;
                }

                foreach ($reservation as $value) {
                    $sumqty += $value->getQty();
                }

                if(($sumqty + $qty) > $maxQty) {
                    return false;
                }
            } catch (NoSuchEntityException $e) {

            }
        }

        $product = $this->productRepository->getById($productId);
        $sku = $product->getSku();
        $sourceItems = $this->sourceItemHelper->getSourceItem($sku, $sourceCode);

        if($qty != null) {
            $newStock = 0;
            foreach($sourceItems as $item) {
                $newStock = $item->getQuantity() - $qty;
            }

            if($newStock <= $buffer) {
                return false;
            }
        }

        return true;
    }
}
