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

namespace CTCD\Category\Model\ResourceModel\CategoryImportMap;

use CTCD\Category\Model\CategoryImportMap;
use CTCD\Category\Api\Data\CategoryImportMapInterface;
use CTCD\Category\Model\ResourceModel\CategoryImportMap as ResourceModel;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = CategoryImportMapInterface::ENTITY_ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'CTCD_CategoryImportMap';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'CTCD_CategoryImportMap';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CategoryImportMap::class, ResourceModel::class);
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
}
