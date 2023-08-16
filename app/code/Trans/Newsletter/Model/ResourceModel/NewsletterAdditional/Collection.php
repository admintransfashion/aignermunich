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

namespace Trans\Newsletter\Model\ResourceModel\NewsletterAdditional;

use Trans\Newsletter\Model\NewsletterAdditional;
use Trans\Newsletter\Api\Data\NewsletterAdditionalInterface;
use Trans\Newsletter\Model\ResourceModel\NewsletterAdditional as ResourceModel;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = NewsletterAdditionalInterface::ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_newsletter_additional';
    
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'trans_newsletter_additional';

    /**
     * Define resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(NewsletterAdditional::class, ResourceModel::class);
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
