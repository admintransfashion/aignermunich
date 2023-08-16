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

namespace Trans\Reservation\Block\Adminhtml\Form\Field\Select;

/**
 * SortNumber
 */
class SortNumber extends \Magento\Backend\Block\Template
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    protected function _toHtml()
    {
        $inputId = $this->getInputId();
        $inputName = $this->getInputName();
        $colName = $this->getColumnName();
        $column = $this->getColumn();
        $value = $this->getValue();

        $string = '<input type="number" readonly name="' . $inputName . '" <%- ' . $colName . ' %> ' . ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' . (isset($column['class']) ? $column['class'] : 'input-text') . '" value="<%- ' .$colName . ' %>">';

        return $string;
    }
}
