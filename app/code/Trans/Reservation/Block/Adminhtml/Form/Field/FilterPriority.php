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

namespace Trans\Reservation\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class FilterPriority
 */
class FilterPriority extends AbstractFieldArray
{
    /**
     * @var string
     */
    protected $_template = 'Trans_Reservation::system/config/form/field/array.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Trans\Reservation\Block\Adminhtml\Form\Field\Select\Filter $selectFilter
     * @param \Trans\Reservation\Block\Adminhtml\Form\Field\Select\SortNumber $sortField
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Trans\Reservation\Block\Adminhtml\Form\Field\Select\Filter $selectFilter,
        \Trans\Reservation\Block\Adminhtml\Form\Field\Select\SortNumber $sortField,
        array $data = []
    ) {
        $this->selectFilter = $selectFilter;
        $this->sortField = $sortField;

        parent::__construct($context, $data);
    }

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('sort', ['label' => __('No'), 'class' => 'required-entry', 'renderer' => $this->sortField]);
        $this->addColumn('filter', ['label' => __('Filter'), 'class' => 'required-entry', 'renderer' => $this->selectFilter]);
        $this->_addAfter = false;
    }
}
