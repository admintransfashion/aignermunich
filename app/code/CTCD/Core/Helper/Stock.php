<?php

namespace CTCD\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite;

/**
 * Class Stock
 */
class Stock extends AbstractHelper
{
    /**
     * @var array
     */
    protected $instances = [];
    
    /**
     * @var GetAssignedStockIdForWebsite
     */
    private $getAssignedStockIdForWebsite;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\InventoryApi\Api\StockRepositoryInterface
     */
    protected $stockRepository;

    /**
     * GetStockIdForWebsite constructor.
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\InventoryApi\Api\StockRepositoryInterface $stockRepository
     * @param GetAssignedStockIdForWebsite $getAssignedStockIdForWebsite
     */
    public function __construct(
        Context $context, 
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\InventoryApi\Api\StockRepositoryInterface $stockRepository,
        GetAssignedStockIdForWebsite $getAssignedStockIdForWebsite
    )
    {
        parent::__construct($context);
        $this->getAssignedStockIdForWebsite = $getAssignedStockIdForWebsite;
        $this->storeManager = $storeManager;
        $this->stockRepository = $stockRepository;
    }

    /**
     * get current website stock
     *
     * @return \Magento\InventoryApi\Api\Data\StockInterface
     */
    public function getCurrentWebsiteStock()
    {
        $website = $this->getCurrentWebsite();
        $stockId = $this->getAssignedStockIdForWebsite->execute($website->getCode());
        $stock = $this->stockRepository->get($stockId);

        return $stock;
    }

    /**
     * get current website
     *
     * @return Magento\Store\Api\Data\WebsiteInterface|null
     */
    public function getCurrentWebsite()
    {
        try {
            $website = $this->storeManager->getWebsite();
        } catch (LocalizedException $localizedException) {
            $website = null;
            $this->logger->error($localizedException->getMessage());
        }

        return $website;
    }

    /**
     * get current stock active
     *
     * @return \Magento\InventoryApi\Api\Data\StockInterface
     */
    public function getCurrentStock()
    {
        if(!isset($this->instances['stock'])) {
            $stock = $this->getCurrentWebsiteStock();
            if($stock) {
                $this->instances['stock'] = $stock;
            }
        }

        return $this->instances['stock'];
    }
}
