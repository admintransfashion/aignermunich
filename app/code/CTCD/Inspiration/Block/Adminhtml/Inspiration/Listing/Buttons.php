<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Block\Adminhtml\Inspiration\Listing;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;

class Buttons extends Container
{

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param AuthorizationInterface $authorization
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->authorization = $context->getAuthorization();
    }

    /**
     * Prepare button and grid
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if($this->authorization->isAllowed('CTCD_Inspiration::inspiration_update')) {
            $button = [
                'id' => 'add_inspiration',
                'name'  => 'add_inspiration',
                'label' => __('Add Inspiration'),
                'onclick' => "location.href='".$this->getUrl('ctcdinspiration/inspiration/new')."'",
                'class' => 'primary'
            ];

            $this->buttonList->add('add_new', $button);
        }
        return parent::_prepareLayout();
    }
}
