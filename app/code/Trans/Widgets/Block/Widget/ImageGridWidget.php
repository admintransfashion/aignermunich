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

class ImageGridWidget extends \Trans\Widgets\Block\Widget\AbstractWidget
{
    protected $_template = "widget/imagegridwidget.phtml";

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
     * Get Image url based on code
     *
     * @param string $imageId
     * @return array|mixed|string
     */
    public function getImage(string $imageId)
    {
        $imageUrl = '';
        if($imageId){
            $imageId = $imageId . 'image';
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
