<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Block\Inspiration;

class Content extends \Magento\Framework\View\Element\Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'inspiration/content.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \CTCD\Inspiration\Block\Inspiration\Sidebar
     */
    protected $inspirationSidebar;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \CTCD\Inspiration\Block\Inspiration\Sidebar $inspirationSidebar
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \CTCD\Inspiration\Block\Inspiration\Sidebar $inspirationSidebar
    ){
        $this->coreRegistry = $coreRegistry;
        $this->filterProvider = $filterProvider;
        $this->inspirationSidebar = $inspirationSidebar;
        parent::__construct($context);
    }

    /**
     * Get html of inspiration content
     *
     * @return string
     * @throws \Exception
     */
    public function getContentHtml()
    {
        $html = '';
        $currentInspiration = $this->coreRegistry->registry('current_inspiration');
        if($currentInspiration){
            $html = $this->filterProvider->getPageFilter()->filter($currentInspiration->getContent());
        }
        return $html;
    }

    /**
     * @return array
     */
    public function getSidebarMenus()
    {
        return $this->inspirationSidebar->getSidebarMenus();
    }
}

