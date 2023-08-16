<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Widgets
 * @license  Proprietary
 *
 * @author   HaDi <ashadi.sejati@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Widgets\Block\Adminhtml\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement as Element;
use Magento\Backend\Block\Template;

class SeparatorField extends Template
{
    /**
     * Prepare element HTML
     *
     * @param Element $element
     * @return Element
     */
    public function prepareElementHtml(Element $element)
    {
        $config = $this->_getData('config');
        $title = isset($config['separator']['title']) ? $config['separator']['title'] : '';
        $titleColor = isset($config['separator']['titleColor']) ? $config['separator']['titleColor'] : '#eb5202';
        $backgroundColor = isset($config['separator']['backgroundColor'] )? $config['separator']['backgroundColor'] : '#f8f8f8';
        $borderColor = isset($config['separator']['borderColor']) ? $config['separator']['borderColor'] : '#e3e3e3';
        $html = '<div style="width:100%;display:block;text-align:left;padding:10px;border-top:1px solid '.$borderColor.';border-bottom:1px solid '.$borderColor.';font-size:16px;text-transform:uppercase;background-color:'.$backgroundColor.';color:'.$titleColor.';"><span>'.$title.'</span></div>';
        $element->setData('after_element_html', $html);

        return $element;
    }
}
