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

use Trans\CatalogMultisource\Api\Data\InventoryPosUpdateResponseInterface;
 
/**
 * Class InventoryPosUpdateResponse
 */
class InventoryPosUpdateResponse extends \Magento\Framework\Model\AbstractExtensibleModel implements InventoryPosUpdateResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(InventoryPosUpdateResponseInterface::MESSAGE);
    }
 
    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(InventoryPosUpdateResponseInterface::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(InventoryPosUpdateResponseInterface::CODE);
    }
 
    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(InventoryPosUpdateResponseInterface::CODE, $code);
    }
}