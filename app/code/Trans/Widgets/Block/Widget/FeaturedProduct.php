<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Widgets
 * @license  Proprietary
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Widgets\Block\Widget;

class FeaturedProduct extends \Trans\Widgets\Block\Widget\AbstractWidget
{
    protected $_template = 'widget/featuredproduct.phtml';

    /**
     * Get Image URL
     * @return string|null
     */
    public function getImageUrl()
    {
        $mediaUrl = $this->ctcdCoreUrlHelper->getMediaUrl();
        $imageUrl = $this->getData('catalogimg');
        if($imageUrl){
            if(strpos($imageUrl, $mediaUrl) === false){
                $imageUrl = $mediaUrl . $imageUrl;
            }
        }
        else{
            $imageUrl = $mediaUrl.'aigner/noimage.png';
        }

        return $imageUrl;
    }
}
