<?php
/**
 * @category Trans
 * @package  Trans_Catalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Controller\Adminhtml\Season;
 
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
/**
 * Class Form
 */
class Form extends \Magento\Backend\App\Action
{
 
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Trans_Catalog::form_season';
 
    /**
     * @var PageFactory 
     */
    protected $resultPageFactory;
 
    /**
     * @var \Trans\Catalog\Controller\Adminhtml\Season
     */
    protected $Season;
 
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context, 
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        
        parent::__construct($context);
    }
 
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $idSeason = $this->getRequest()->getParam('id');

        $title = __('Create Season');

        if($idSeason) {
            $title = __('Edit Season');
        }
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Trans_Catalog::form_season')
                ->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
 
}