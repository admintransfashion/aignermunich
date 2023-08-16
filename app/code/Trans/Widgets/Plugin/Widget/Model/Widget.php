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

namespace Trans\Widgets\Plugin\Widget\Model;

class Widget
{
    const IMAGE_CHOOSER_FIELDS = [
        'topleftimage',
        'topcenterimage',
        'toprightimage',
        'bottomleftimage',
        'bottomcenterimage',
        'bottomrightimage',
        'catalogimg',
        'bannerimg1',
        'bannerimg2',
        'imagegrid1',
        'imagegrid2',
        'imagegrid3',
        'imagegrid4'
    ];

    /**
     * @var \CTCD\Core\Helper\Url
     */
    protected $ctcdCoreUrlHelper;

    /**
     * @param \CTCD\Core\Helper\Url $ctcdCoreUrlHelper
     */
    public function __construct(
        \CTCD\Core\Helper\Url $ctcdCoreUrlHelper
    ) {
        $this->ctcdCoreUrlHelper = $ctcdCoreUrlHelper;
    }

    public function beforeGetWidgetDeclaration(
        \Magento\Widget\Model\Widget $subject,
        $type,
        $params = [],
        $asIs = true
    ){
        $domain = $this->ctcdCoreUrlHelper->getMediaUrl();
        foreach (self::IMAGE_CHOOSER_FIELDS as $field) {
            if(key_exists($field, $params)) {
                $imageUrl = $params[$field];
                if(strpos($imageUrl, $domain) !== false) {
                    $imageUrl = str_replace($domain, '', $imageUrl);
                    $params[$field] = $imageUrl;
                }
            }
        }
        return array($type, $params, $asIs);
    }
}
