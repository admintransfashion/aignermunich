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

class View extends \Magento\Framework\App\Action\Action
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
     * @var \CTCD\Inspiration\Api\InspirationRepositoryInterface
     */
    protected $inspirationRepository;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \CTCD\Inspiration\Api\InspirationRepositoryInterface $inspirationRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \CTCD\Inspiration\Api\InspirationRepositoryInterface $inspirationRepository
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->inspirationRepository = $inspirationRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $inspirationId = $this->getRequest()->getParam('id', false);
        $resultForward = $this->resultForwardFactory->create();
        if($inspirationId){
            $inspiration = $this->inspirationRepository->getById($inspirationId);
            if($inspiration && $inspiration->getIsActive() == 1){
                $this->coreRegistry->register('current_inspiration', $inspiration);
                $page = $this->resultPageFactory->create();
                $page->getConfig()->getTitle()->set(__('Inspiration'));
                $page->getConfig()->getTitle()->prepend($inspiration->getTitle());
                return $page;
            }
            else{
                return $resultForward->forward('noroute');
            }
        }
        else{
            return $resultForward->forward('noroute');
        }
    }
}
