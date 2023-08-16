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

use Trans\CatalogMultisource\Api\Data\DefaultResponseInterface;
 
/**
 * Class InventoryPosUpdateResponse
 */
class DefaultResponse extends \Magento\Framework\Model\AbstractExtensibleModel implements DefaultResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(DefaultResponseInterface::MESSAGE);
    }
 
    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(DefaultResponseInterface::MESSAGE, $message);
    }
   
}