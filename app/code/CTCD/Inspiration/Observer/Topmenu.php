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

namespace CTCD\Inspiration\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;

class Topmenu implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory
     */
    protected $inspirationCollectionFactory;

    /**
     * @var \CTCD\Inspiration\Helper\Data
     */
    protected $inspirationHelper;

    /**
     * @param \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory $inspirationCollectionFactory
     * @param \CTCD\Inspiration\Helper\Data $inspirationHelper
     */
    public function __construct(
        \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory $inspirationCollectionFactory,
        \CTCD\Inspiration\Helper\Data $inspirationHelper
    ){
        $this->inspirationCollectionFactory  = $inspirationCollectionFactory;
        $this->inspirationHelper  = $inspirationHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->inspirationHelper->isModuleEnabled() && $this->inspirationHelper->isAddedToTopmenu()) {

            /** @var \Magento\Framework\Data\Tree\Node $parentNode */
            $parentNode = $observer->getMenu();

            $data = [
                'name' => __($this->inspirationHelper->getMenuLabel()),
                'id' => 'inspiration',
                'url' => '/inspiration/list.html',
                'is_active' => true
            ];

            $inspirationNode = new Node($data, 'id', $parentNode->getTree(), $parentNode);
            $parentNode->addChild($inspirationNode);

            if($this->inspirationHelper->addChildToMenu()){
                /**
                 * Get all active inspirations with include_in_menu = 1
                 */
                $inspirations = $this->inspirationCollectionFactory->create();
                $inspirations->addFieldToFilter('is_active', ['eq' => 1]);
                $inspirations->addFieldToFilter('include_in_menu', ['eq' => 1]);
                $inspirations->setOrder('sort_order', 'ASC');
                $inspirations->setOrder('entity_id', 'ASC');

                if($inspirations){
                    foreach($inspirations as $item){
                        $childData = [
                            'name' => __($item->getTitle()),
                            'id' => $item->getUrlKey(),
                            'url' => '/inspiration/'.$item->getUrlKey().'.html',
                            'is_active' => true
                        ];
                        $childNode = new Node($childData, 'id', $inspirationNode->getTree(), $inspirationNode);
                        $inspirationNode->addChild($childNode);
                    }
                }
            }
        }

        return $this;
    }
}
