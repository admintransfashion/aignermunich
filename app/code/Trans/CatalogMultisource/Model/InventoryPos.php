<?php
/**
 * @category Trans
 * @package  Trans_CatalogMultisource
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CatalogMultisource\Model;

use Trans\CatalogMultisource\Api\Data\DefaultResponseInterface;
use Trans\CatalogMultisource\Api\InventoryPosInterface;

/**
 * @api
 * Update inventory from POS data
 */
class InventoryPos implements InventoryPosInterface
{
	/**
     * @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface
     */
    protected $getSourceItem;

    /**
	 * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
	 */
	protected $sourceItemSave;

	/**
	 * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
	 */
	protected $sourceItem;

	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request;

	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $json;

	/**
	 * @var \Trans\CatalogMultisource\Api\Data\DefaultResponseInterfaceFactory
	 */
	protected $response;

	/**
	 * @var \Trans\CatalogMultisource\Helper\SourceItem
	 */
	protected $sourceItemHelper;

	/**
	 * @var \Trans\CatalogMultisource\Helper\Oauth
	 */
    protected $oauthHelper;
    
    /**
	 * @var \Trans\CatalogMultisource\Helper\ValidateRequest
	 */
    protected $validate;
    
    /**
	 * @var \Trans\CatalogMultisource\Helper\Exceptions
	 */
	protected $exception;

	/**
     * @param \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItem
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Trans\CatalogMultisource\Api\Data\DefaultResponseInterfaceFactory $response
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
     * @param \Trans\CatalogMultisource\Helper\Oauth $oauthHelper
     * @param \Trans\CatalogMultisource\Helper\ValidateRequest $validate
     * @param \Trans\CatalogMultisource\Helper\Exceptions $exception
     */
    public function __construct(
    	\Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItem,
    	\Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave,
    	\Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem,
    	\Magento\Framework\App\RequestInterface $request,
    	\Magento\Framework\Serialize\Serializer\Json $json,
    	\Trans\CatalogMultisource\Api\Data\DefaultResponseInterfaceFactory $response,
    	\Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper,
        \Trans\CatalogMultisource\Helper\Oauth $oauthHelper,
        \Trans\CatalogMultisource\Helper\ValidateRequest $validate,
        \Trans\CatalogMultisource\Helper\Exceptions $exception
    ) {
        $this->getSourceItem = $getSourceItem;
        $this->sourceItemSave = $sourceItemSave;
        $this->sourceItem = $sourceItem;
        $this->request = $request;
        $this->json = $json;
        $this->response = $response;
        $this->sourceItemHelper = $sourceItemHelper;
        $this->oauthHelper = $oauthHelper;
        $this->validate = $validate;
        $this->exception = $exception;
    }

	/**
     * {@inheritdoc}
     */
    public function execute() {
        //Get Request & Convert
        $request = $this->request->getContent();
        $data = $this->json->unserialize($request);

        //Validate Request
        $this->validate->one('items',$data);
        $this->validate->many(['sku','source_code','qty'],$data['items']);

        // Check & Deduct Request
        $params = $this->checkSourceItem($data['items']);

        // Set Request to Items
        $items = $this->setSourceItem($params);

        // Save Items
        try {
            $this->sourceItemSave->execute($items);
            $this->sourceItemHelper->stockItemReindex();
        } catch (\Exception $e) {
            $this->exception->add(500,$e->getMessage().InventoryPosInterface::ERR_SOURCE_ITEM);
        }
        // Response
        $result = $this->response->create();
        $result->setMessage(__(InventoryPosInterface::MSG_SUCCESS));
        return  $result;
    }

    /**
     * Generate stock update payload
     * 
     * @param array $params
     * @return array
     */
    protected function checkSourceItem($params)
    {
        $result = [];
        try {
            if(is_array($params)) {
                foreach ($params as $value) {
                    $sku = $value['sku'];
                    $sourceCode = $value['source_code'];
                    $sourcesItem = $this->sourceItemHelper->getSourceItem($sku, $sourceCode);
                    
                    $qty = 0;
                    foreach ($sourcesItem as $sourceItem) {
                        $qty = $sourceItem->getQuantity() - $value['qty'];
                    }
                    if($qty > 0) {
                        $value['qty'] = (int)$qty;
                    }
                    $result[] = $value;	
                }
            }
        } catch (\Exception $e) {
            $this->exception->add(500,$e->getMessage());
        }
    	
    	return $result;
    }

    /**
     * Set source stock item
     *
     * @param array $params
     * @return bool
     */
    protected function setSourceItem($params) {
        $result = [];
        try {
            foreach($params as $param) {
                $sourceItem = $this->sourceItem->create();
                $sourceItem->setSku($param['sku']);
                $sourceItem->setSourceCode($param['source_code']);
                $sourceItem->setQuantity($param['qty']);
                $sourceItem->setStatus(1);
                
                $result[] = $sourceItem;
            }
        } catch (\Exception $e) {
            return  $this->exception->add(500,$e->getMessage());
        }
        return $result;
    }

    /**
     * Send email notification
     * 
     * @param array $emptyProduct
     * @return void
     */
    protected function sendEmailNotifcation($emptyProduct)
    {
        $token = $this->oauthHelper->getCurrentToken();
        
        if($token) {
            $user = $this->oauthHelper->getAdminIdByToken($token);
            $adminId = $user['admin_id'];
            $admin = $this->oauthHelper->getAdminById($adminId);
            
            $this->sourceItemHelper->sendEmailNotif($admin->getEmail(), $emptyProduct);
        }
        
        return;
    }
}