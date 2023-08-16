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

class EditorialImageGrid extends \Trans\Widgets\Block\Widget\AbstractWidget
{
    protected $_template = 'widget/editorialimagegrid.phtml';

    /**
     * Get Media URL
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->ctcdCoreUrlHelper->getMediaUrl();
    }

    /**
     * Get widget title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title') ? $this->escapeHtml($this->getData('title')): '';
    }

    /**
     * Get widget description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getData('description') ? $this->escapeHtml($this->getData('description'), ['br']) : '';
    }

    /**
     * Get Image url based on ID
     *
     * @param int $imageId
     * @return array|mixed|string
     */
    public function getImage(int $imageId)
    {
        $imageUrl = '';
        if($imageId && is_int($imageId) &&  (int) $imageId > 0){
            $imageId = 'imagegrid' . $imageId;
            if($imageUrl = $this->getData($imageId)){
                if(strpos($imageUrl, $this->getMediaUrl()) === false){
                    $imageUrl = $this->getMediaUrl() . $imageUrl;
                }
            }
            else{
                $imageUrl = $this->getMediaUrl().'aigner/noimage.png';
            }
        }
        return $imageUrl;
    }
}
