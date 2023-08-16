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

use Magento\Store\Model\ScopeInterface;
use Magento\Inventory\Model\SourceItem\Command\Handler\SourceItemsSaveHandler;

use Magento\Inventory\Model\SourceItem\Command\SourceItemsSave as MageSourceItemsSave;
use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface;
use Trans\CatalogMultisource\Api\Data\InventoryUpdateResponseInterface;
use Trans\CatalogMultisource\Api\InventoryUpdateInterface;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Validation\ValidationException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @inheritdoc
 */
class InventoryUpdate implements InventoryUpdateInterface
{
    /**
     * @var SourceItemsSaveHandler
     */
    private $sourceItemsSaveHandler;

    /**
     * @var Trans\CatalogMultisource\Api\Data\SourceItemSaveResponseInterfaceFactory
     */
    protected $sourceUpdateHistory;

    /**
     * @var Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface
     */
    protected $sourceUpdateRepository;

    /**
     * @var \Trans\CatalogMultisource\Helper\Oauth
     */
    protected $oauthHelper;

    /**
     * @var \Trans\CatalogMultisource\Api\Data\InventoryPosUpdateResponseInterfaceFactory
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
     * @param SourceItemsSaveHandler $sourceItemsSaveHandler
     * @param Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory $sourceUpdateHistory
     * @param Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface $sourceUpdateRepository
     * @param \Trans\CatalogMultisource\Api\Data\InventoryPosUpdateResponseInterfaceFactory $response
     * @param \Trans\CatalogMultisource\Helper\Oauth $oauthHelper
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
     * @param \Trans\CatalogMultisource\Helper\Data $dataHelper
     */
    public function __construct(
        SourceItemsSaveHandler $sourceItemsSaveHandler,
        \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory $sourceUpdateFactory,
        \Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface $sourceUpdateRepository,
        \Trans\CatalogMultisource\Api\Data\InventoryPosUpdateResponseInterfaceFactory $response,
        \Trans\CatalogMultisource\Helper\Oauth $oauthHelper,
        \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper,
        \Trans\CatalogMultisource\Helper\Data $dataHelper
    )
    {
        $this->sourceItemsSaveHandler = $sourceItemsSaveHandler;
        $this->sourceUpdateFactory = $sourceUpdateFactory;
        $this->sourceUpdateRepository = $sourceUpdateRepository;
        $this->oauthHelper = $oauthHelper;
        $this->response = $response;
        $this->sourceItemHelper = $sourceItemHelper;
        $this->dataHelper = $dataHelper;
        $this->logger = $this->dataHelper->getLogger();
    }

    /**
     * @inheritdoc
     */
    public function execute(array $sourceItems)
    {
        try {
            $this->sourceItemsSaveHandler->execute($sourceItems);

            $this->sourceItemHelper->stockItemReindex();
            // if($this->validateParam($sourceItems)) {
            // }
        } catch (InputException $e) {
            $this->logger->info('Source inventory update error. Message = ' . $e->getMessage());
            return $this->getResponse(InventoryUpdateResponseInterface::STATUS_ERROR_GENERAL, null, $e->getMessage());
        } catch (ValidationException $e) {
            $this->logger->info('Source inventory update error. Message = ' . $e->getMessage());
            return $this->getResponse(InventoryUpdateResponseInterface::STATUS_ERROR_GENERAL, null, $e->getMessage());
        } catch (CouldNotSaveException $e) {
            $this->logger->info('Source inventory update error. Message = ' . $e->getMessage());
            return $this->getResponse(InventoryUpdateResponseInterface::STATUS_ERROR_GENERAL, null, $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->info('Source inventory update error. Message = ' . $e->getMessage());
            return $this->getResponse(InventoryUpdateResponseInterface::STATUS_ERROR_GENERAL, null, $e->getMessage());
        }

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
            $this->logger->info('Save source inventory update history error. Message = ' . $e->getMessage());
        }

        return $this->getResponse(InventoryUpdateResponseInterface::STATUS_SUCCESS, null, 'Source Item updated.');
    }

    /**
     * Generate error response
     * 
     * @param string $field
     * @return Trans\CatalogMultisource\Api\Data\UpdateSourceItemInterface
     */
    protected function getResponse($code = InventoryUpdateResponseInterface::STATUS_SUCCESS, $field = null, $message = null) {
        /** \Trans\CatalogMultisource\Api\Data\UpdateSourceItemInterfaceFactory */
        $result = $this->response->create();

        $result->setMessage(__($message));
        
        if($code === InventoryUpdateResponseInterface::STATUS_ERROR_FIELD_REQUIRED) {
            $result->setMessage(__('%1 field is required.', $field));
        }
        
        $result->setCode($code);

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

    /**
     * validate post param key
     * 
     * @param array $params
     * @param return bool
     */
    protected function validateParam(array $params)
    {
        $keys = InventoryUpdateInterface::PARAMS_KEY;
    
        $paramKeys = $this->array_keys_multi($params);
        
        $diff = array_diff($paramKeys, $keys);
        
        if(count($diff) > 0) {
            $stringKey = implode(',', $diff);
            throw new \Exception(__("Undefined index %1", $stringKey), 1);
        }

        return true;
    }

    /**
     * get all array keys
     * 
     * @param array $array
     * @return array
     */
    public function array_keys_multi(array $array)
    {
        $keys = array();

        foreach ($array as $key => $value) {
            if($key == (int)$key) {
                continue;
            }
            $keys[] = $key;

            if (is_array($value)) {
                $keys = array_merge($keys, $this->array_keys_multi($value));
            }
        }

        return $keys;
    }
}
