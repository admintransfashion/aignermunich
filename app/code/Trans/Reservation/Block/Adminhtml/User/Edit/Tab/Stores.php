<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Trans\Reservation\Block\Adminhtml\User\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;

/**
 * @api
 * @since 100.0.2
 */
class Stores extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory
     */
    protected $_userRolesFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory
     */
    protected $sourceCollection;

    /**
     * @var \Trans\Reservation\Api\Data\UserStoreRepositoryInterface
     */
    protected $userStoreRepository;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $userRolesFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $sourceCollection
     * @param \Trans\Reservation\Api\Data\UserStoreRepositoryInterface $userStoreRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $userRolesFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $sourceCollection,
        \Trans\Reservation\Api\UserStoreRepositoryInterface $userStoreRepository,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_userRolesFactory = $userRolesFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->sourceCollection = $sourceCollection;
        $this->userStoreRepository = $userStoreRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('permissionsStoresGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('asc');
        $this->setTitle(__('Stores'));
        $this->setUseAjax(true);
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        parent::_addColumnFilterToCollection($column);
        
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->sourceCollection->create();
        $collection->addFieldToFilter('source_code', ['neq' => 'default']);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'assigned_store',
            [
                'header_css_class' => 'data-grid-actions-cell',
                'header' => __('Assigned'),
                'type' => 'checkbox',
                'html_name' => 'stores[]',
                'field_name' => 'stores[]',
                'values' => $this->getSelectedStore(),
                'align' => 'center',
                'index' => 'source_code'
            ]
        );

        $this->addColumn('store_name', ['header' => __('Store'), 'index' => 'name']);

        return parent::_prepareColumns();
    }

    // /**
    //  * @return string
    //  */
    // public function getGridUrl()
    // {
    //     $userPermissions = $this->_coreRegistry->registry('permissions_user');
    //     return $this->getUrl('*/*/rolesGrid', ['user_id' => $userPermissions->getUserId()]);
    // }

    /**
     * @param bool $json
     * @return array|string
     */
    public function getSelectedStore()
    {
        // var_dump($this->getRequest()->getParams());
        $userId = $this->getRequest()->getParam('user_id'); 
        
        $stores = $this->userStoreRepository->getByUserId($userId);

        $arrayStores = [];
        
        foreach ($stores as $store) {
            $arrayStores[] = $store->getStoreCode();
        }

        return $arrayStores;
    }
}
