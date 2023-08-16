<?php
/**
 * @category Trans
 * @package  Trans_CatalogMultisource
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CatalogMultisource\Model\ResourceModel\SourceItemUpdateHistory;

use Trans\CatalogMultisource\Model\Source\SourceItemUpdateHistory;
use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface;
use Trans\CatalogMultisource\Model\ResourceModel\SourceItemUpdateHistory as ResourceModel;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = SourceItemUpdateHistoryInterface::ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sourceitem_update_history_collection';
    
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'sourceitem_update_history_collection';

    /**
     * Define resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(SourceItemUpdateHistory::class, ResourceModel::class);
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