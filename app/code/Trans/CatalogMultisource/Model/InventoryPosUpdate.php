<?php
/**
 * @category Trans
 * @package  Trans_CatalogMultisource
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CatalogMultisource\Model;

use Trans\CatalogMultisource\Api\Data\InventoryPosUpdateResponseInterface;
use Trans\CatalogMultisource\Api\InventoryPosUpdateInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 * Update inventory from POS data
 */
class InventoryPosUpdate implements InventoryPosUpdateInterface
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
	 * @var \Trans\CatalogMultisource\Api\Data\InventoryPosUpdateResponseInterfaceFactory
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
     * @param \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItem
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Trans\CatalogMultisource\Api\Data\InventoryPosUpdateResponseInterfaceFactory $response
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
     * @param \Trans\CatalogMultisource\Helper\Oauth $oauthHelper
     */
    public function __construct(
    	\Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItem,
    	\Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave,
    	\Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem,
    	\Magento\Framework\App\RequestInterface $request,
    	\Magento\Framework\Serialize\Serializer\Json $json,
    	\Trans\CatalogMultisource\Api\Data\InventoryPosUpdateResponseInterfaceFactory $response,
    	\Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper,
    	\Trans\CatalogMultisource\Helper\Oauth $oauthHelper
    ) {
        $this->getSourceItem = $getSourceItem;
        $this->sourceItemSave = $sourceItemSave;
        $this->sourceItem = $sourceItem;
        $this->request = $request;
        $this->json = $json;
        $this->response = $response;
        $this->sourceItemHelper = $sourceItemHelper;
        $this->oauthHelper = $oauthHelper;
    }

	/**
     * {@inheritdoc}
     */
    public function execute() {
        try {
            $postRequest = $this->request->getContent();
            $fieldname = InventoryPosUpdateInterface::FIELDNAME;

            $data = $this->json->unserialize($postRequest);
            
            $validateSourceItems = $this->fieldValidation($data, $fieldname); 
            if($validateSourceItems == false) {
            	return $this->getResponse(InventoryPosUpdateResponseInterface::STATUS_ERROR_FIELD_REQUIRED, $fieldname);
            }

            $params = $data[$fieldname];

            $params = $this->generatePayload($params);
            
            $items = [];
            foreach($params as $param) {
                $sourceItem = $this->sourceItem->create();
                $sourceItem->setSku($param['sku']);
                $sourceItem->setSourceCode($param['source_code']);
                $sourceItem->setQuantity($param['quantity']);
                $sourceItem->setStatus(1);
                
                $items[] = $sourceItem;
            }

            return $this->saveSourceItem($items);
        } catch (\Exception $e) {
            return $this->getResponse(InventoryPosUpdateResponseInterface::STATUS_ERROR_GENERAL, null, $e->getMessage());
        }
    }

    /**
     * Generate stock update payload
     * 
     * @param array $params
     * @return array
     */
    protected function generatePayload($params)
    {
    	$data = [];
    	if(is_array($params)) {
    		foreach ($params as $value) {
    			$sku = $value['sku'];
    			$sourceCode = $value['source_code'];
    			$sourcesItem = $this->sourceItemHelper->getSourceItem($sku, $sourceCode);
    			
    			$qty = 0;
    			foreach ($sourcesItem as $sourceItem) {
    				$qty = $sourceItem->getQuantity() - $value['quantity'];
    			}

    			if($qty > 0) {
    				$value['quantity'] = (int)$qty;
    			}
    			
    			$data[] = $value;	
    		}
    	}
    	
    	return $data;
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

    /**
     * Save source stock item
     *
     * @param array $data
     * @return bool
     */
    protected function saveSourceItem($data) {
        try {
            $this->sourceItemSave->execute($data);
            $this->sourceItemHelper->stockItemReindex();
            return $this->getResponse(InventoryPosUpdateResponseInterface::STATUS_SUCCESS, null, 'Source Item updated.');
        } catch (\Exception $e) {
        	return $this->getResponse(InventoryPosUpdateResponseInterface::STATUS_ERROR_GENERAL, null, $e->getMessage());
        }
    }

    /**
     * Validate param fileds
     *
     * @param array $array
     * @param bool
     */
    protected function fieldValidation($array, $field) {
    	if(isset($array[$field])) {
            return true;
        }

        return false;
    }

    /**
     * Generate error response
     * 
     * @param string $field
     * @return Trans\CatalogMultisource\Api\Data\UpdateSourceItemInterface
     */
    protected function getResponse($status = 2, $field = null, $message = null) {
        /** \Trans\CatalogMultisource\Api\Data\UpdateSourceItemInterfaceFactory */
        $result = $this->response->create();

        $result->setMessage(__($message));
        
        if($status === InventoryPosUpdateResponseInterface::STATUS_ERROR_FIELD_REQUIRED) {
            $result->setMessage(__('%1 field is required.', $field));
        }
        
        $result->setCode($status);

        return $result;
    }	
}