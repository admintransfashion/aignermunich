<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Model\ResourceModel\CategoryImport;

use CTCD\Category\Model\CategoryImport;
use CTCD\Category\Api\Data\CategoryImportInterface;
use CTCD\Category\Model\ResourceModel\CategoryImport as ResourceModel;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = CategoryImportInterface::ENTITY_ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'CTCD_CategoryImport';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'CTCD_CategoryImport';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CategoryImport::class, ResourceModel::class);
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Select::GROUP);

        return $countSelect;
    }

    /**
     * initSelect
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->setOrder(CategoryImportInterface::ENTITY_ID, 'DESC');
    }
}
