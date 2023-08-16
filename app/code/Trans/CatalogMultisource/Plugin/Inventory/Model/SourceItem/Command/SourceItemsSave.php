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
namespace Trans\CatalogMultisource\Plugin\Inventory\Model\SourceItem\Command;

use Magento\Inventory\Model\SourceItem\Command\SourceItemsSave as MageSourceItemsSave;
use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class SourceItemsSave
 */
class SourceItemsSave
{
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
     * @var \Trans\CatalogMultisource\Helper\SourceItem
     */
    protected $sourceItemHelper;

    /**
     * @param Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory $sourceUpdateHistory
     * @param Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface $sourceUpdateRepository
     * @param \Trans\CatalogMultisource\Helper\Oauth $oauthHelper
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
     */
    public function __construct(
    	\Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory $sourceUpdateFactory,
    	\Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface $sourceUpdateRepository,
        \Trans\CatalogMultisource\Helper\Oauth $oauthHelper,
        \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
    )
    {
        $this->sourceUpdateFactory = $sourceUpdateFactory;
        $this->sourceUpdateRepository = $sourceUpdateRepository;
        $this->oauthHelper = $oauthHelper;
        $this->sourceItemHelper = $sourceItemHelper;
    }
	
	/**
     * Plugin save source item update history
     *
     * @param MageSourceItemsSave $subject
     * @param callable $proceed
     * @param array $sourceItems
     * @return Trans\CatalogMultisource\Api\Data\SourceItemSaveResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function aroundExecute(MageSourceItemsSave $subject, callable $proceed, array $sourceItems)
    {
    	$emptyProduct = [];
		$actualResult = $proceed($sourceItems);
    	
    	foreach ($sourceItems as $key => $sourceItem) {
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

        return $actualResult;
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