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

namespace Trans\CatalogMultisource\Model\ResourceModel;

use Magento\Framework\Stdlib\DateTime\DateTime as LibDateTime;
use Magento\Framework\Model\AbstractModel;
use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface;

/**
 * Class SourceItemUpdateHistory
 */
class SourceItemUpdateHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    /**
     * @var LibDateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * Construct
     *
     * @param Context $context
     * @param DateTime $date
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        LibDateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->date = $date;
        $this->timezone = $timezone;
    
        parent::__construct($context);
    }
    
    /**
     * Initialize resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(SourceItemUpdateHistoryInterface::TABLE_NAME, SourceItemUpdateHistoryInterface::ID);
    }

    /**
     * save updatedat
     * @SuppressWarnings(PHPMD)
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            if (!$object->hasCreatedAt()) {
                $object->setCreatedAt($this->date->gmtDate());
            }
        }

        $object->setUpdatedAt($this->date->gmtDate());

        $date = $this->timezone->date();
        $date = $date->format('Y-m-d');
        $object->setLastExecuted($date);

        return parent::_beforeSave($object);
    }
}