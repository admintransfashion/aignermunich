<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Core\Model\Config\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;

class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var  \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @param Context $context
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // set wysiwyg for element
        $element->setWysiwyg(true);
        // set configuration values
        $element->setConfig($this->getWysiwygConfig());
        return parent::_getElementHtml($element);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    protected function getWysiwygConfig()
    {
        $config = $this->wysiwygConfig->getConfig();
        $configData = $config->getData();
        $configData['add_variables'] = false;
        $configData['add_widgets'] = false;
        $configData['add_images'] = false;
        $configData['add_directives'] = false;
        $configData['plugins'] = [];
        $configData['files_browser_window_url'] = '';
        $configData['files_browser_window_width'] = 0;
        $configData['files_browser_window_height'] = 0;
        $config->setData($configData);

        return $config;
    }
}
