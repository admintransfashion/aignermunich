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

namespace CTCD\Inspiration\Block\Adminhtml\Inspiration\Edit\Buttons;

class Delete extends Generic implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getDataId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete action-secondary',
                'on_click' => 'deletePrompt("Delete", \'' . __('<span style="color:#eb5202">This action cannot be canceled.</span><br/>Are you sure you want to delete this inspiration?<br/><br/><br/>Type "<strong>delete</strong>" to confirm the action:') . '\',"delete", \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 30,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getDataId()]);
    }
}
