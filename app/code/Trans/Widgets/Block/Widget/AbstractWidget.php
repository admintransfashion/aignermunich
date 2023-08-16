<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Widgets
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Widgets\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class AbstractWidget extends Template implements BlockInterface
{
    protected $_template = '';

    /**
     * @var \CTCD\Core\Helper\Url
     */
    protected $ctcdCoreUrlHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \CTCD\Core\Helper\Url $ctcdCoreUrlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \CTCD\Core\Helper\Url $ctcdCoreUrlHelper,
        array $data = []
    ) {
        $this->ctcdCoreUrlHelper = $ctcdCoreUrlHelper;
        parent::__construct($context, $data);
    }
}
