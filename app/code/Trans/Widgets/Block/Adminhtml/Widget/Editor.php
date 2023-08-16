<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Widgets
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Widgets\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;

class Editor extends Element
{
    /**
     * @var Factory
     */
    protected $_elementFactory;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param array   $data
     * @param Config  $config
     */
    public function __construct(Context $context, Factory $elementFactory, Config $config, array $data = [])
    {
        $this->_elementFactory = $elementFactory;
        $this->_wysiwygConfig  = $config;
        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML.
     *
     * @param AbstractElement $element Form Element
     *
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $input = $this->_elementFactory->create('editor', ['data' => $element->getData()])
            ->setId($element->getId())
            ->setClass('widget-option input-textarea admin__control-text')
            ->setWysiwyg(true)
            ->setConfig($this->getWysiwygConfig())
            ->setForceLoad(true)
            ->setForm($element->getForm());
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        $element->setData('after_element_html', $this->_getAfterElementHtml().$input->getElementHtml());

        return $element;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    protected function getWysiwygConfig()
    {
        $config = $this->_wysiwygConfig->getConfig();
        $configData = $config->getData();
        $configData['add_variables'] = false;
        $configData['add_widgets'] = false;
        $configData['add_images'] = false;
        $configData['add_directives'] = false;
        $configData['plugins'] = [];
        $configData['files_browser_window_url'] = '';
        $configData['files_browser_window_width'] = 0;
        $configData['files_browser_window_height'] = 0;
        $configData['tinymce4']['toolbar'] = 'bold italic underline';
        $configData['tinymce4']['plugins'] = '';
        $config->setData($configData);

        return $config;
    }

    /**
     * @return string
     */
    protected function _getAfterElementHtml()
    {
        $html = <<<HTML
    <style>
        .admin__field-control.control .control-value {
            display: none !important;
        }
    </style>
HTML;

        return $html;
    }
}
