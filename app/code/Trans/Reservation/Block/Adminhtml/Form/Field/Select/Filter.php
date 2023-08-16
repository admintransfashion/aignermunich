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
 * Filter
 */
class Filter extends \Magento\Backend\Block\Template
{
    /**
     * @var \Trans\Reservation\Model\Config\Source\FilterPriority
     */
    protected $filterPriority;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Trans\Reservation\Model\Config\Source\FilterPriority $filterPriority
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Magento\Config\Model\Config\Source\Yesno $configYesno
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Trans\Reservation\Model\Config\Source\FilterPriority $filterPriority,
        array $data = []
    ) {
        $this->filterPriority = $filterPriority;
        parent::__construct($context, $data);
    }

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

        $string = '<select id="' . $inputId . '"' .
            ' name="' . $inputName . '" <%- ' . $colName . ' %> ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') .
            ' class="' . (isset($column['class']) ? $column['class'] : 'input-text') . '">';
        foreach ($this->filterPriority->toOptionArray() as $row) {
            $string .= '<option value="' . $row['value'] . '">' . $row['label'] . '</option>';
        }
        $string .= '</select>';

        return $string;
    }
}
