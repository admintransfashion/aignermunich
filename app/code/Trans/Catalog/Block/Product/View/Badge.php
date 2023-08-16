<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Catalog
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Catalog\Block\Product\View;

class Badge extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Trans\Catalog\Helper\Product
     */
    protected $transProductHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Trans\Catalog\Helper\Product $transProductHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Trans\Catalog\Helper\Product $transProductHelper,
        array $data = []
    ) {
        $this->transProductHelper = $transProductHelper;
        parent::__construct($context, $data);
    }

    public function getBadge()
    {
        return $this->transProductHelper->getProductBadge($this->getProduct());
    }
}
