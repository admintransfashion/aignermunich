<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Widgets
 * @license  Proprietary
 *
 * @author   HaDi <ashadi.sejati@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Widgets\Block\Widget;

class NosHomepageWidget extends \Trans\Widgets\Block\Widget\AbstractWidget
{
    protected $_template = "widget/noshomepagewidget.phtml";

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @var \Trans\Gtm\Helper\Data
     */
    protected $gtmHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \CTCD\Core\Helper\Url $ctcdCoreUrlHelper
     * @param \Trans\Gtm\Helper\Data $gtmHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \CTCD\Core\Helper\Url $ctcdCoreUrlHelper,
        \Trans\Gtm\Helper\Data $gtmHelper,
        array $data = []
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->gtmHelper = $gtmHelper;
        parent::__construct($context, $ctcdCoreUrlHelper, $data);
    }

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

    /**
     * Generate dataLayer 'shop_now_homepage' event
     *
     * @return string
     */
    public function generateDataLayer()
    {
        $dataLayer = [
            'event' => 'shop_now_homepage',
            'user_id' => $this->gtmHelper->getCurrentCustomerId()
        ];
        $dataLayerString = 'dataLayer.push('. $this->jsonSerializer->serialize($dataLayer).');';
        return $dataLayerString;
    }
}
