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
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Trans\Reservation\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Trans\Reservation\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Trans\Reservation\Logger\Logger $logger
    )
    {
        parent::__construct($context);

        $this->logger = $logger;
    }

    /**
     * get logger
     * 
     * @return Trans\Reservation\Logger\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}