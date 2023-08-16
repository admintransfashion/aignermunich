<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Reservation
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Reservation\Plugin\CatalogInventory\Helper;

class Stock
{
    /**
     * @var \Trans\Reservation\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Trans\Reservation\Helper\Config $configHelper
     */
    public function __construct(
        \Trans\Reservation\Helper\Config $configHelper
    ){
        $this->configHelper = $configHelper;
    }

    public function afterAddIsInStockFilterToCollection(
        \Magento\CatalogInventory\Helper\Stock $subject,
        $result,
        $collection
    ){
        $bufferStock = (int) $this->configHelper->getGlobalProductBuffer();
        if($this->configHelper->isEnabled() && $bufferStock > 0) {
            $queryString = (string) $collection->getSelect();
            if (strpos($queryString, 'inventory_stock_2_buffer') === false) {
                $collection->getSelect()->join(
                    ['inventory_stock_2_buffer' => $collection->getTable('inventory_stock_2')],
                    'product.sku = inventory_stock_2_buffer.sku AND inventory_stock_2_buffer.quantity > ' . $bufferStock,
                    []
                );
            }
        }

        return $collection;
    }
}
