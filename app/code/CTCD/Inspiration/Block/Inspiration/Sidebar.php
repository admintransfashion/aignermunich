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

class Sidebar extends \Magento\Framework\View\Element\Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'inspiration/sidebar.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
     protected $customerSession;

    /**
     * @var \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory
     */
    protected $inspirationCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory $inspirationCollectionFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory $inspirationCollectionFactory
    ){
        $this->coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->inspirationCollectionFactory  = $inspirationCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Get sidebar menu html
     *
     * @return array
     */
    public function getSidebarMenus()
    {
        $menus = [];

        $currentInspiration = $this->coreRegistry->registry('current_inspiration');
        $currentInspirationId = $currentInspiration ? $currentInspiration->getId() : null;

        $inspirations = $this->inspirationCollectionFactory->create();
        $inspirations->addFieldToFilter('is_active', ['eq' => 1]);
        $inspirations->setOrder('sort_order', 'ASC');
        $inspirations->setOrder('entity_id', 'ASC');

        if($inspirations){
            foreach($inspirations as $item){
                $isActive = ($currentInspirationId == $item->getId()) ? true : false;
                $menus[] = [
                    'title' => $item->getTitle(),
                    'active' => $isActive,
                    'url' => $isActive ? 'javascript:void(0);' : $this->getUrl('inspiration').$item->getUrlKey().'.html',
                    'onclick' => "dataLayer.push({'event': 'inspiration_navigation','menu_name': '".$item->getTitle()."','user_id': '".$this->getCustomerIdCustom()."'})"
                ];
            }
        }
        return $menus;
    }

    /**
     * get customer id
     * @return string
     */
    public function getCustomerIdCustom()
    {
        $customerCode = 'Not Login yet';
        if ($this->customerSession->getCustomer()->getEmail()) {
            $customerCode = hash('sha256', $this->customerSession->getCustomer()->getEmail());
        }
        return $customerCode;
    }
}

