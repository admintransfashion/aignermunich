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
namespace Trans\CatalogMultisource\Api;
 
/**
 * @api
 */
interface InventoryPosUpdateInterface
{
	/**
	 * Raw body post data
	 */
	const FIELDNAME = 'posData';
    
    /**
     * Update product inventory by POS
     *
     * @return Trans\CatalogMultisource\Api\Data\InventoryPosUpdateResponseInterface
     */
    public function execute();
}