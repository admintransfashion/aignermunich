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

class ImagePreview extends Template
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
        $html = '';
        if(isset($config['preview']['image'])){
            $imageUrl = $this->getViewFileUrl($config['preview']['image']);
            $html = '<h3>Widget Preview</h3>';
            $html .= '<img src="'.$imageUrl.'" style="width:auto; border:1px solid #e3e3e3" />';
        }
        $element->setData('after_element_html', $html);

        return $element;
    }
}
