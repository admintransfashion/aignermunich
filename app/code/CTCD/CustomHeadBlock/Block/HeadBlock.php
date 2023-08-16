<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_CustomHeadBlock
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\CustomHeadBlock\Block;

class HeadBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'head_block.phtml';

    /**
     * @var \CTCD\CustomHeadBlock\Helper\Data
     */
    protected $customHeadHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \CTCD\CustomHeadBlock\Helper\Data $customHeadHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \CTCD\CustomHeadBlock\Helper\Data $customHeadHelper
    ){
        $this->customHeadHelper = $customHeadHelper;
        parent::__construct($context);
    }

    /**
     * Get html of inspiration content
     *
     * @return string
     * @throws \Exception
     */
    public function getFacebookData()
    {
        $html = '';

        if($this->customHeadHelper->isModuleEnabled() && $this->customHeadHelper->isFacebookEnabled() && $this->customHeadHelper->getFacebookMetaName() && $this->customHeadHelper->getFacebookMetaValue()){
            $html = '<meta name="'.$this->customHeadHelper->getFacebookMetaName().'" content="'.$this->customHeadHelper->getFacebookMetaValue().'" />';
        }
        return $html;
    }
}

