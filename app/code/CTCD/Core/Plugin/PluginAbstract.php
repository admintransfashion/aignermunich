<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Core\Plugin;

abstract class PluginAbstract
{
    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \CTCD\Core\Helper\Data
     */
    protected $ctcdCoreHelper;

    /**
     * @param \CTCD\Core\Helper\Data $ctcdCoreHelper
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \CTCD\Core\Helper\Data $ctcdCoreHelper
    ) {
        $this->registry = $registry;
        $this->urlBuilder = $urlBuilder;
        $this->ctcdCoreHelper = $ctcdCoreHelper;
    }
}
