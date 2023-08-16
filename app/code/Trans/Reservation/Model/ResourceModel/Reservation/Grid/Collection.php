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

namespace Trans\Reservation\Model\ResourceModel\Reservation\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use Trans\Reservation\Model\ResourceModel\Reservation\Collection as resCollection;
use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;

class Collection extends resCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $eventPrefix
     * @param string $eventObject
     * @param string $resourceModel
     * @param string $model
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|string|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $eventPrefix,
        $mainTable,
        $eventObject,
        $resourceModel,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * get add collection query
     *
     * @return this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $attributeId = $objectManager->create('Magento\Eav\Model\Entity\Attribute')->getIdByCode('customer', 'telephone');
        $user = $objectManager->create('Magento\Backend\Model\Auth\Session')->getUser();
        $userStores = $objectManager->create('Trans\Reservation\Api\UserStoreRepositoryInterface')->getByUserId($user->getUserId());

        // $this->addAttributeToSelect('item.' . ReservationItemInterface::SOURCE_CODE, 'item.' . ReservationItemInterface::REFERENCE_NUMBER, 'item.' . ReservationItemInterface::START_DATE, 'item.' . ReservationItemInterface::START_TIME, 'item.' . ReservationItemInterface::FLAG, 'item.' . ReservationItemInterface::RESERVATION_ID);

        $this->getSelect()
            ->join(array('item' => ReservationItemInterface::TABLE_NAME), 'main_table.id= item.reservation_id',
                ['item.' . ReservationItemInterface::SOURCE_CODE, 'item.' . ReservationItemInterface::REFERENCE_NUMBER, 'item.' . ReservationItemInterface::START_DATE, 'item.' . ReservationItemInterface::START_TIME, 'item.' . ReservationItemInterface::FLAG, 'item.' . ReservationItemInterface::RESERVATION_ID, 'item.' . ReservationItemInterface::BUSINESS_STATUS]
        );

        $this->getSelect()
            ->join(array('ce1' => 'customer_entity'), 'ce1.entity_id=main_table.customer_id', array('firstname' => 'firstname', 'lastname' => 'lastname'))
            ->columns(new \Zend_Db_Expr("CONCAT(`ce1`.`firstname`, ' ',`ce1`.`lastname`) AS fullname"));

        $this->getSelect()
            ->joinLeft( array('customer_att' => 'customer_entity_varchar'), 'customer_att.entity_id = main_table.customer_id', array('customer_att.value'))->where('attribute_id', $attributeId)
            ->columns(new \Zend_Db_Expr("value AS phone"));

        $this->getSelect()->group('item.reservation_id');

        $this->addFieldToFilter('main_table.' . ReservationInterface::FLAG, ['neq' => ReservationInterface::FLAG_NEW]);
        $this->addFilterToMap('phone', 'customer_att.value');
        $this->addFilterToMap('fullname', new \Zend_Db_Expr("CONCAT(`ce1`.`firstname`, ' ',`ce1`.`lastname`)"));

        $stores = [];
        foreach($userStores as $store) {
            $stores[] = $store->getStoreCode();
        }

        if(count($stores) != 0) {
            $this->addFieldToFilter('item.' . ReservationItemInterface::SOURCE_CODE, ['in' => $stores]);
        }

        $this->setOrder('item.' . ReservationItemInterface::END_DATE, 'DESC');

        return $this;
    }

    /**
     * get current admin user
     *
     * @return \Magento\User\Model\User
     */
    protected function getCurrentUser()
    {
        // var_dump($this->resourceModel->getUser());
        // var_dump($this->session->getStore());
        return $this->authSession;
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
}
