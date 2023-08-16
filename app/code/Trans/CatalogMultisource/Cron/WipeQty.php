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

namespace Trans\CatalogMultisource\Cron;

use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface;

/**
 * Class WipeQty
 */
class WipeQty
{
	/**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory
     */
    protected $inventoryHistory;

    /**
     * @var \Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface
     */
    protected $inventoryHistoryRepository;

    /**
     * @param \Magento\Framework\App\ResourceConnection $connection
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     * @param \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory $inventoryHistory
     * @param \Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface $inventoryHistoryRepository
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $connection,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory $inventoryHistory,
        \Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface $inventoryHistoryRepository
    ) {
        $this->connection = $connection;
        $this->indexerFactory = $indexerFactory;
        $this->inventoryHistory = $inventoryHistory;
        $this->inventoryHistoryRepository = $inventoryHistoryRepository;
    }

    /**
     * Wipe out product qty by raw sql query
     * 
     * @return void
     */
    public function execute() 
    {
        $connection = $this->connection->getConnection();
        $tableName = $this->connection->getTableName('inventory_source_item');
        $sql = "Update " . $tableName . " Set quantity = 0";
        
        try {
            /* execute query */
            $connection->query($sql);

            /* reindex data inventory */
            $indexer = $this->indexerFactory->create()->load('inventory');
            $indexer->reindexAll();

            $message = 'wipe out product stock success!';
            $flag = 'success';    
        } catch (\Exception $e) {
            $message = 'wipe out product stock error!. Error message = ' . $e->getMessage();    
            $flag = 'failed';    
        }
        
        $history = $this->inventoryHistory->create();
        $history->setFlag($flag);
        $history->setPayload('query = ' . $sql . ' - message = ' . $message);
        $history->setSku('all');
        
        try {
            $this->inventoryHistoryRepository->save($history);
        } catch (\Exception $e) {
            
        }
    }
}
