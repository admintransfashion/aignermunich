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

namespace Trans\CatalogMultisource\Controller\Index;

use Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterface;

/**
 * 
 */
class TestUpdateStock extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
	 */
	protected $sourceItemSave;

	/**
     * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
     */
    protected $sourceItem;

    /**
     * @var Trans\CatalogMultisource\Api\Data\SourceItemSaveResponseInterfaceFactory
     */
    protected $sourceUpdateHistory;

    /**
     * @var Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface
     */
    protected $sourceUpdateRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem,
        \Trans\CatalogMultisource\Api\Data\SourceItemUpdateHistoryInterfaceFactory $sourceUpdateFactory,
        \Trans\CatalogMultisource\Api\SourceItemUpdateHistoryRepositoryInterface $sourceUpdateRepository
    ) {
        parent::__construct($context);
        $this->sourceItemSave = $sourceItemSave;
        $this->sourceItem = $sourceItem;
        $this->sourceUpdateFactory = $sourceUpdateFactory;
        $this->sourceUpdateRepository = $sourceUpdateRepository;
    }

    public function execute() {
        $json = '{
  "sourceItems": [{
    "sku": "CAPT-AMR1-Blue",
    "source_code": "senayan-city",
    "quantity": 1009,
    "status": 1
  },
  {
    "sku": "CAPT-AMR-21",
    "source_code": "grand-indo",
    "quantityt": 2,
    "status": 1
  }]
}';
// var_dump($json);
// var_dump(json_decode($json));
        $params = [
            [
                "sku" => "24-MB01",
                "source_code" => "senayan-city",
                "quantity" => 150,
                "status" => 1
            ],
            [
                "sku" => "24-MB04",
                "source_code" => "grand-indonesia",
                "quantity" => 210,
                "status" => 1
            ],
            [
                "sku" => "24-MB04",
                "source_code" => "grand-indonesia",
                "quantity" => 210,
                "status" => 1
            ],
            [
                "sku" => "24-MB04",
                "source_code" => "grand-indonesia",
                "quantity" => 210,
                "status" => 1
            ],
            [
                "sku" => "24-MB04",
                "source_code" => "grand-indonesia",
                "quantity" => 210,
                "status" => 1
            ],
            [
                "sku" => "24-MB04",
                "source_code" => "grand-indonesia",
                "quantity" => 210,
                "status" => 1
            ],
            [
                "sku" => "24-MB04",
                "source_code" => "grand-indonesia",
                "quantity" => 210,
                "status" => 1
            ]
        ];
$keys = ['sku', 'source_code', 'quantity', 'status'];
// var_dump($params);

$paramKeys = $this->array_keys_multi($params);
$paramKeys[] = 'qty'; 
$diff = array_diff($paramKeys, $keys);
var_dump($diff);
// var_dump(count($diff));
die();

        foreach($params as $param) {
            // $sourceItem = $this->sourceItem->create();
            // $sourceItem->setSku($param['sku']);
            // $sourceItem->setSourceCode($param['source_code']);
            // $sourceItem->setQuantity($param['quantity']);
            // $sourceItem->setStatus($param['status']);
            
            // $data[] = $sourceItem;

            $update = $this->sourceUpdateFactory->create();
            $update->setFlag(SourceItemUpdateHistoryInterface::FLAG_SUCCESS);
            $update->setSku($param['sku']);
            $update->setSourceCode($param['source_code']);
            $update->setQuantity((int)$param['quantity']);
            $update->setStatus(1);
            
            $this->sourceUpdateRepository->save($update);
        }

        // $this->saveSourceItem($data);
    }

    /**
     * Save source stock item
     * @param array $data
     * @return bool
     */
    protected function saveSourceItem($data) {
        try {
            $this->sourceItemSave->execute($data);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

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