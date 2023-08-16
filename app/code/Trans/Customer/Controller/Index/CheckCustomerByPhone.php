<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Controller\Index;

use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class CheckCustomerByPhone
 */
class CheckCustomerByPhone extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
     */
    function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
    )
    {   
        parent::__construct($context);
        $this->customerFactory = $customerFactory;
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * check customer by phone
     * @return json bool
     */
    public function execute() 
    {
        $resultJson = $this->resultJsonFactory->create();
        $response = false;
        
        try {
          $content = $this->request->getContent();
          $data = json_decode($content);
        
          $telephone = $data->telephone;
          
          if(!strpos($telephone, '@') !== false ) 
          {
              /* Get email id based on mobile number and login*/
              $customereCollection = $this->customerFactory->create();
              $customereCollection->addFieldToFilter("telephone", $telephone);
              
              if($customereCollection->getSize() > 0) {
                  foreach($customereCollection as $customerdata){ 
                      $response = true;
                  }
              }
          }

        } catch (\Exception $e) {
          $response = false;
        }
        
        $resultJson->setData($response);
        
        return $resultJson;
    }
}