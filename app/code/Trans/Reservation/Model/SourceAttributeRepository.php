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

use Trans\Reservation\Api\SourceAttributeRepositoryInterface;
use Trans\Reservation\Api\Data\SourceAttributeInterface;
use Trans\Reservation\Api\Data\SourceAttributeInterfaceFactory;
use Trans\Reservation\Api\Data\SourceAttributeSearchResultsInterfaceFactory;
use Trans\Reservation\Model\ResourceModel\SourceAttribute as ResourceModel;
use Trans\Reservation\Model\ResourceModel\SourceAttribute\Collection;
use Trans\Reservation\Model\ResourceModel\SourceAttribute\CollectionFactory;

/**
 * Class SourceAttributeRepository
 */
class SourceAttributeRepository implements SourceAttributeRepositoryInterface
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
     * @var SourceAttributeResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var SourceAttributeInterfaceFactory
     */
    protected $sourceAttribute;

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
     * @param SourceAttributeResultsInterfaceFactory $searchResultsFactory
     * @param SourceAttributeInterfaceFactory $sourceAttribute
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param StoreManagerInterface $storeManager
     * @param \Trans\Reservation\Helper\Config $configHelper
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        SourceAttributeSearchResultsInterfaceFactory $searchResultsFactory,
        SourceAttributeInterfaceFactory $sourceAttribute,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        \Trans\Reservation\Helper\Config $configHelper
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->sourceAttribute = $sourceAttribute;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SourceAttributeInterface $data)
    {
    	/** @var SourceAttributeInterface|\Magento\Framework\Model\AbstractModel $data */
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
    public function saveData(array $data)
    {
        /** @var Array|\Magento\Framework\Model\AbstractModel $data */
        try {
            if(isset($data['attribute'])) {
                foreach ($data['attribute'] as $key => $value) {
                    $attr = $this->get($key, $data['sourceCode']);
                    $attr->setValue($value);
                    $attr->setSourceCode($data['sourceCode']);
                    $attr->setAttribute($key);
                    $this->save($attr);
                }
            }
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
    public function get($attribute, $sourceCode)
    {
        /** @var \Trans\Reservation\Api\Data\SourceAttributeInterface|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(SourceAttributeInterface::ATTRIBUTE, $attribute);
        $collection->addFieldToFilter(SourceAttributeInterface::SOURCE_CODE, $sourceCode);

        $getLastCollection = $collection->getLastItem();

        try {
            $data = $this->getById($getLastCollection->getId());
        } catch (NoSuchEntityException $e) {
            $data = $this->sourceAttribute->create();
        }

        $this->instances[$sourceCode.$attribute] = $data;

        return $this->instances[$sourceCode.$attribute];
    }

    /**
     * {@inheritdoc}
     */
    public function getById($dataId)
    {
        if (!isset($this->instances[$dataId])) {
            /** @var \Trans\Reservation\Api\Data\SourceAttributeInterface|\Magento\Framework\Model\AbstractModel $reservation */
            $data = $this->sourceAttribute->create();
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
    public function getBySourceCode($code)
    {
        /** @var \Trans\Reservation\Api\Data\SourceAttributeInterface|\Magento\Framework\Model\AbstractModel $reservation */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter(SourceAttributeInterface::SOURCE_CODE, $code);

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
        /** @var \Trans\Reservation\Api\Data\SourceAttributeResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Trans\Reservation\Model\ResourceModel\SourceAttribute\Collection $collection */
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

        /** @var \Trans\Reservation\Api\Data\SourceAttributeInterface[] $reservation */
        $reservations = [];
        /** @var \Trans\Reservation\Model\Reservation $reservation */
        foreach ($collection as $reservation) {
            /** @var \Trans\Reservation\Api\Data\SourceAttributeInterface $reservationDataObject */
            $reservationDataObject = $this->sourceAttribute->create();
            $this->dataObjectHelper->populateWithArray($reservationDataObject, $reservation->getData(), SprintResponseInterface::class);
            $reservations[] = $reservationDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($reservations);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SourceAttributeInterface $data)
    {
        /** @var \Trans\Reservation\Api\Data\SourceAttributeInterface|\Magento\Framework\Model\AbstractModel $reservation */
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
