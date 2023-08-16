<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy Surya Perdana <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Controller\Index;

use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;


class CheckCustomer extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Customer\Model\Customer
     */
    protected $customerModel;

    public $observer;

    function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Customer $customerModel

    )
    {   
        parent::__construct($context);
        $this->customerModel = $customerModel;
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute() 
    {
        $resultJson = $this->resultJsonFactory->create();
        $register = $this->request->getParam('telephone');
        $customerData = $this->customerModel->getCollection()
                      ->addAttributeToFilter('telephone', $register)
                      ->getFirstItem();

         //check data telephone is already exist or not
        if($customerData->getTelephone()){
            echo $customerData->getTelephone();
        }else{
            return false;
        }
}
}