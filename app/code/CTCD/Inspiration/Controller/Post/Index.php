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

namespace CTCD\Inspiration\Controller\Post;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory
     */
    protected $inspirationCollectionFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory $inspirationCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory $inspirationCollectionFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->inspirationCollectionFactory = $inspirationCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $inspirations = $this->inspirationCollectionFactory->create();
        $inspirations->addFieldToFilter('is_active', ['eq' => 1]);
        $inspirations->setOrder('sort_order', 'ASC');
        $inspirations->setOrder('entity_id', 'ASC');
        $inspirations->getSelect()->limit(1);

        if($inspirations && count($inspirations) > 0){
            $page = $this->resultPageFactory->create();
            foreach($inspirations as $inspiration){
                $this->coreRegistry->register('current_inspiration', $inspiration);
                $page->getConfig()->getTitle()->set(__('Inspiration'));
                $page->getConfig()->getTitle()->prepend($inspiration->getTitle());
            }
            return $page;
        }
        else{
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
    }
}
