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

namespace Trans\Reservation\Block\Customer\Reservation;

use Trans\Reservation\Api\Data\ReservationInterface;
/**
 * Class ReserveList
 */
class ReserveList extends AbstractBlock
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Trans\Reservation\Api\Data\ReservationInterfaceFactory
     */
    protected $reservationFactory;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Api\Data\ReservationInterfaceFactory $reservationFactory
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Api\Data\ReservationInterfaceFactory $reservationFactory,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->currentCustomer = $currentCustomer;
        $this->dataHelper = $dataHelper;
        $this->reservationFactory = $reservationFactory;
        $this->reservationRepository = $reservationRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * get reservation list collection
     *
     * @return Trans\Reservation\Model\ResourceModel\Reservation\Collection
     */
    public function getList(){
        $customerId = $this->currentCustomer->getCustomerId();

        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;

        $model = $this->collectionFactory->create();
        $model->addFieldToFilter(ReservationInterface::CUSTOMER_ID, $customerId);
        $model->addFieldToFilter('flag', array('neq' => ReservationInterface::FLAG_NEW));
        $model->setOrder('id', 'DESC');

        $model->setPageSize($pageSize);
        $model->setCurPage($page);

        return $model;
    }

     public function getCustomCollection()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest(

        )->getParam('limit') : 5;
        $collection = $this->customCollection->getCollection();
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getList()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'reservation.history.pager'
            )->setAvailableLimit([5 => 5, 10 => 10, 15 => 15, 20 => 20])
                ->setShowPerPage(true)->setCollection(
                    $this->getList()
                );
            $this->setChild('pager', $pager);
            $this->getList()->load();
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * change date format
     *
     * @param datetime $datetime
     * @param string $format
     * @return datetime
     */
    public function changeDateFormat($datetime, $format = 'd F Y H:i')
    {
        return $this->dataHelper->changeDateFormat($datetime, $format);
    }

    /**
     * get reservation status
     *
     * @param string $flag
     * @return string
     */
    public function getReservationStatus(string $flag)
    {
        $active = ReservationInterface::FLAG_ACTIVE_ARRAY;
        if(in_array(strtolower($flag), $active)) {
            return ucwords(ReservationInterface::FLAG_ACTIVE);
        }

        return ucwords(ReservationInterface::FLAG_INACTIVE);
    }
}
