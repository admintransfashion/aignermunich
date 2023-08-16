<?php
/**
 * @category Trans
 * @package  Trans_Newsletter
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Newsletter\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

use Trans\Newsletter\Api\NewsletterAdditionalRepositoryInterface;
use Trans\Newsletter\Api\Data\NewsletterAdditionalInterface;
use Trans\Newsletter\Api\Data\NewsletterAdditionalInterfaceFactory;
use Trans\Newsletter\Api\Data\NewsletterAdditionalSearchResultsInterfaceFactory;
use Trans\Newsletter\Model\ResourceModel\NewsletterAdditional as ResourceModel;
use Trans\Newsletter\Model\ResourceModel\NewsletterAdditional\Collection;
use Trans\Newsletter\Model\ResourceModel\NewsletterAdditional\CollectionFactory;

/**
 * Class NewsletterAdditionalRepository
 */
class NewsletterAdditionalRepository implements NewsletterAdditionalRepositoryInterface
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
     * @var NewsletterAdditionalResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var NewsletterAdditionalInterfaceFactory
     */
    protected $newsletterAdditional;

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
     * @param NewsletterAdditionalResultsInterfaceFactory $searchResultsFactory
     * @param NewsletterAdditionalInterfaceFactory $newsletterAdditional
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceModel $resource,
        Collection $collection,
        CollectionFactory $collectionFactory,
        NewsletterAdditionalSearchResultsInterfaceFactory $searchResultsFactory,
        NewsletterAdditionalInterfaceFactory $newsletterAdditional,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->collection = $collection;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->newsletterAdditional = $newsletterAdditional;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(NewsletterAdditionalInterface $data)
    {
    	/** @var NewsletterAdditionalInterface|\Magento\Framework\Model\AbstractModel $data */
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
            /** @var \Trans\Newsletter\Api\Data\NewsletterAdditionalInterface|\Magento\Framework\Model\AbstractModel $newsletterAdditional */
            $data = $this->newsletterAdditional->create();
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
    public function getBySubscriberId($subscriberId)
    {
        if (!isset($this->instances[$subscriberId])) {
            /** @var \Trans\Newsletter\Api\Data\NewsletterAdditionalInterface|\Magento\Framework\Model\AbstractModel $newsletterAdditional */
            $data = $this->newsletterAdditional->create();
            $this->resource->load($data, $subscriberId, 'subscriber_id');
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested Data Response doesn\'t exist'));
            }
            $this->instances[$subscriberId] = $data;
        }
        return $this->instances[$subscriberId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Trans\Newsletter\Api\Data\NewsletterAdditionalResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Trans\Newsletter\Model\ResourceModel\NewsletterAdditional\Collection $collection */
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

        /** @var \Trans\Newsletter\Api\Data\NewsletterAdditionalInterface[] $newsletterAdditional */
        $newsletterAdditionals = [];
        /** @var \Trans\Newsletter\Model\Reservation $newsletterAdditional */
        foreach ($collection as $newsletterAdditional) {
            /** @var \Trans\Newsletter\Api\Data\NewsletterAdditionalInterface $newsletterAdditionalDataObject */
            $newsletterAdditionalDataObject = $this->newsletterAdditional->create();
            $this->dataObjectHelper->populateWithArray($newsletterAdditionalDataObject, $newsletterAdditional->getData(), SprintResponseInterface::class);
            $newsletterAdditionals[] = $newsletterAdditionalDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($newsletterAdditionals);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(NewsletterAdditionalInterface $data)
    {
        /** @var \Trans\Newsletter\Api\Data\NewsletterAdditionalInterface|\Magento\Framework\Model\AbstractModel $newsletterAdditional */
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
