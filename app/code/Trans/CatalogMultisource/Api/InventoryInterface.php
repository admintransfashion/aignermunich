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

namespace Trans\CatalogMultisource\Api;

/**
 * Service method for source items save multiple
 * Performance efficient API
 *
 * Used fully qualified namespaces in annotations for proper work of WebApi request parser
 *
 * @api
 */
interface InventoryInterface
{
    const MSG_SUCCESS = 'Successfully updated Source Item.';
    const ERR_SOURCE_ITEM = ' / Source item is not register.' ;

    /**
     * Update product inventory
     *
     * @return Trans\CatalogMultisource\Api\Data\DefaultResponseInterface
     */
    public function execute();
}
