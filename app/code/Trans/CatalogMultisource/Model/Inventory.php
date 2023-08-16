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

use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface;
use Trans\CatalogMultisource\Api\Data\DefaultResponseInterface;
use Trans\CatalogMultisource\Api\InventoryInterface;



/**
 * @inheritdoc
 */
class Inventory implements InventoryInterface
{


    /**
	 * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
	 */
    protected $sourceItem;
    
    /**
	 * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
	 */
	protected $sourceItemSave;

    /**
     * @var Trans\CatalogMultisource\Api\Data\SourceItemSaveResponseInterfaceFactory
     */
    protected $sourceUpdateHistory;

    /**
     * @var Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface
     */
    protected $sourceUpdateRepository;

    /**
	 * @var \Magento\Framework\App\RequestInterface
	 */
    protected $request;
    
    /**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $json;

    /**
     * @var \Trans\CatalogMultisource\Helper\Oauth
     */
    protected $oauthHelper;

    /**
     * @var \Trans\CatalogMultisource\Api\Data\DefaultResponseInterfaceFactory
     */
    protected $response;

    /**
     * @var \Trans\CatalogMultisource\Helper\SourceItem
     */
    protected $sourceItemHelper;

    /**
     * @var \Trans\CatalogMultisource\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
	 * @var \Trans\CatalogMultisource\Helper\ValidateRequest
	 */
    protected $validate;
    
    /**
	 * @var \Trans\CatalogMultisource\Helper\Exceptions
	 */
	protected $exception;


    /**
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
     * @param Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory $sourceUpdateHistory
     * @param Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface $sourceUpdateRepository
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem
     * @param \Trans\CatalogMultisource\Api\Data\DefaultResponseInterfaceFactory $response
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Trans\CatalogMultisource\Helper\Oauth $oauthHelper
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
     * @param \Trans\CatalogMultisource\Helper\ValidateRequest $validate
     * @param \Trans\CatalogMultisource\Helper\Data $dataHelper
     * @param \Trans\CatalogMultisource\Helper\Exceptions $exception
     */
    public function __construct(
        \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory $sourceUpdateFactory,
        \Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface $sourceUpdateRepository,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Trans\CatalogMultisource\Api\Data\DefaultResponseInterfaceFactory $response,
        \Trans\CatalogMultisource\Helper\Oauth $oauthHelper,
        \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper,
        \Trans\CatalogMultisource\Helper\Data $dataHelper,
        \Trans\CatalogMultisource\Helper\ValidateRequest $validate,
        \Trans\CatalogMultisource\Helper\Exceptions $exception
    )
    {

        $this->sourceUpdateFactory = $sourceUpdateFactory;
        $this->sourceUpdateRepository = $sourceUpdateRepository;
        $this->oauthHelper = $oauthHelper;
        $this->response = $response;
        $this->sourceItemHelper = $sourceItemHelper;
        $this->dataHelper = $dataHelper;
        $this->json = $json;
        $this->logger = $this->dataHelper->getLogger();
        $this->request = $request;
        $this->validate = $validate;
        $this->sourceItem = $sourceItem;
        $this->sourceItemSave = $sourceItemSave;
        $this->exception = $exception;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $request = $this->request->getContent();
        $data = $this->json->unserialize($request);
       
        //Validate Request
        $this->validate->one('items',$data);
        $items = $data['items'];
        $this->validate->many(['sku','source_code','qty'], $items);
        
        // Set Request to Items
        $items = $this->setSourceItem($items);

        // Save Items
        try {
            $this->sourceItemSave->execute($items);
            $this->sourceItemHelper->stockItemReindex();
        } catch (\Exception $e) {
            $this->exception->add(500,$e->getMessage().InventoryInterface::ERR_SOURCE_ITEM);
        }
        $this->updateHistorySourceItem($items);
        
        // Response
        $result = $this->response->create();
        $result->setMessage(__(InventoryInterface::MSG_SUCCESS));

        return $result; 
    }

    /**
     * Set source stock item
     *
     * @param array $params
     * @return bool
     */
    protected function setSourceItem($params) {
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
            $this->exception->add(500,$e->getMessage());
        }
        return $result;
    }

    /**
     * Update History source stock item
     *
     * @param array $sourceItems
     */
    protected function updateHistorySourceItem($sourceItems) {
        
        try {
            $emptyProduct = [];
            foreach ($sourceItems as $sourceItem) {
                $sku = $sourceItem->getSku();
                $update = $this->sourceUpdateFactory->create();

                $update->setSku($sku);
                $update->setSourceCode($sourceItem->getSourceCode());
                $update->setQty($sourceItem->getQuantity());
                $update->setFlag($this->getFlag($sku));

                if(!$this->sourceItemHelper->isProductExists($sku)) {
                    $emptyProduct[] = $sku;
                }
              
                $this->sourceUpdateRepository->save($update);
            }

            $this->sendEmailNotifcation($emptyProduct);
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $this->exception->add(500,$e->getMessage());
        }
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
     * Get process update flag
     * 
     * @param string $sku
     * @return string
     */
    protected function getFlag($sku) {
        $productExists = $this->sourceItemHelper->isProductExists($sku);
        $flag = SourceItemUpdateHistoryInterface::FLAG_HOLD;

        if($productExists === true) {
            $flag = SourceItemUpdateHistoryInterface::FLAG_SUCCESS;
        }

        return $flag;
    }

}
