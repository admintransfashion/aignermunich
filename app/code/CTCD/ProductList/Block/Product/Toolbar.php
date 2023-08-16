<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_ProductList
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\ProductList\Block\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Class Toolbar
 * @package CTCD\ProductList\Block\Product
 */
class Toolbar extends \Trans\Gtm\Preference\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * Get custom base url
     *
     * @return string
     */
    protected function getCustomBaseUrl()
    {
        $parentUrl = $this->getBaseUrl() . \CTCD\ProductList\Block\Product\ProductList::DEFAULT_PARENT_URL . '/';
        $pageType = $this->getPageType();
        switch ($pageType){
            case 'bestseller':
                $baseUrl = $parentUrl . \CTCD\ProductList\Block\Product\Bestseller::DEFAULT_CHILD_URL;
                break;
            case 'newproduct':
                $baseUrl = $parentUrl . \CTCD\ProductList\Block\Product\NewProduct::DEFAULT_CHILD_URL;
                break;
            case 'saleproduct':
                $baseUrl = $parentUrl . \CTCD\ProductList\Block\Product\SaleProduct::DEFAULT_CHILD_URL;
                break;
            default:
                $baseUrl = '';
        }

        return $baseUrl;
    }

    /**
     * Generate request Url
     *
     * @var array $parameters
     * @return string
     */
    public function generateRequestUrl($parameters = [])
    {
        $baseUrl = $this->getCustomBaseUrl();
        $newParameters = [];
        $oldParameters = $this->request->getParams();
        if(isset($oldParameters['id'])){
            unset($oldParameters['id']);
        }
        if(!empty($parameters)){
            foreach ($parameters as $key => $value){
                $oldParameters[$key] = $value;
            }
        }
        foreach ($oldParameters as $key => $value){
            if($value){
                $newParameters[] = $key.'='.$value;
            }
        }
        return !empty($newParameters) ? $baseUrl.'?'.implode('&', $newParameters) : $baseUrl;
    }
}
