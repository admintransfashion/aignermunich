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

namespace Trans\CatalogMultisource\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class SourceItem
 */
class SourceItem extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * const inventory indexer id
     */
    const INVENTORY_INDEXER_ID = 'inventory';

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productModel;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\InventoryIndexer\Indexer\InventoryIndexer
     */
    protected $inventoryIndexer;

    /**
     * @var \Magento\InventoryIndexer\Indexer\Stock\StockIndexer
     */
    protected $stockIndexer;

    /**
     * @var SourceItemRepositoryInterface
     */
    protected $sourceItemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\InventoryIndexer\Indexer\InventoryIndexer $inventoryIndexer
     * @param \Magento\InventoryIndexer\Indexer\Stock\StockIndexer $stockIndexer
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\InventoryIndexer\Indexer\InventoryIndexer $inventoryIndexer,
        \Magento\InventoryIndexer\Indexer\Stock\StockIndexer $stockIndexer,
        SourceItemRepositoryInterface $sourceItemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productModel = $productModel;
        $this->productRepository = $productRepository;
        $this->transportBuilder = $transportBuilder;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->inventoryIndexer = $inventoryIndexer;
        $this->stockIndexer = $stockIndexer;

        parent::__construct($context);
    }

    /**
     * Get Source item by SKU & Source Code
     * 
     * @param string $sku
     * @param string $sourceCode
     * @return Magento\InventoryApi\Api\Data\SourceItemInterface
     */
    public function getSourceItem(string $sku, string $sourceCode)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SKU, $sku)
            ->addFilter(SourceItemInterface::SOURCE_CODE, $sourceCode)
            ->create();

        return $this->sourceItemRepository->getList($searchCriteria)->getItems();
    }

    /**
     * is product exists
     * 
     * @param string $sku
     * @return bool
     */
    public function isProductExists($sku) {
        if($this->productModel->getIdBySku($sku) != null) {
            return true;
        }

        return false;
    }

    /**
     * Send email notification
     * 
     * @param string $emailTo
     * @param array $emptyProduct
     * @return void
     */
    public function sendEmailNotif($emailTo, $emptyProduct = [])
    {
        try {
            $sender = [
                'name' => $this->scopeConfig->getValue('trans_email/ident_general/name',ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE)
            ];
            
            if(!empty($emptyProduct)) {
                $skus = implode(', ', $emptyProduct); 
                $var['content'] = __("Update product stock success, but product(s) with SKU %1 does not exists. You only need to create product with those SKU(s).", $skus);
            } else {
                $var['content'] = __("Update product stock success!");
            }

            $transport = $this->transportBuilder
            ->setTemplateIdentifier('source_item_update_failed') // this code we have mentioned in the email_templates.xml
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars($var)
            ->setFrom($sender)
            ->addTo($emailTo)
            ->getTransport();
             
            $transport->sendMessage();
            
            return;
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * reindex prooduct inventory
     * 
     * @param Magento\InventoryApi\Api\Data $sourceItem
     * @return void;
     */
    public function reindexProductInventory($sourceItem) 
    {
        $this->inventoryIndexer->executeRow($sourceItem->getId());
    }

    /**
     * reindex source items product
     * 
     * @param array $sourceItems
     * @return void
     */
    public function sourceItemsReindex(array $sourceItems)
    {
        foreach($sourceItems as $sourceItem) {
            try {
                $this->reindexProductInventory($sourceItem);
            } catch (NoSuchEntityException $e) {
                continue;
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * reindex product stock
     * 
     * @return void
     */
    public function stockItemReindex()
    {
        $this->stockIndexer->executeFull();
    }
}