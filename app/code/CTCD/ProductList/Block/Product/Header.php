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

class Header extends \Magento\Framework\View\Element\Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'product/header.phtml';

    /**
     * @var \CTCD\Core\Helper\Url
     */
    protected $coreUrlHelper;

    /**
     * @var \CTCD\ProductList\Helper\Data
     */
    protected $productListHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \CTCD\Core\Helper\Url $coreUrlHelper
     * @param \CTCD\ProductList\Helper\Data $productListHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \CTCD\Core\Helper\Url $coreUrlHelper,
        \CTCD\ProductList\Helper\Data $productListHelper
    ){
        $this->coreUrlHelper = $coreUrlHelper;
        $this->productListHelper = $productListHelper;
        parent::__construct($context);
    }

    /**
     * Get header title
     *
     * @return string
     */
    public function getTItle()
    {
        $pageType = $this->getPageType();
        switch ($pageType){
            case 'bestseller':
                $title = $this->productListHelper->getBestsellerTitle();
                break;
            case 'newproduct':
                $title = $this->productListHelper->getNewProductTitle();
                break;
            case 'saleproduct':
                $title = $this->productListHelper->getSaleProductTitle();
                break;
            default:
                $title = '';
        }
        return $title;
    }

    /**
     * Get header image url
     *
     * @return string
     */
    public function getImageUrl()
    {
        $pageType = $this->getPageType();
        switch ($pageType){
            case 'bestseller':
                $image = $this->productListHelper->getBestsellerImage();
                break;
            case 'newproduct':
                $image = $this->productListHelper->getNewProductImage();
                break;
            case 'saleproduct':
                $image = $this->productListHelper->getSaleProductImage();
                break;
            default:
                $image = '';
        }
        return $image ? $this->coreUrlHelper->getMediaUrl() . 'ctcd/productlist/' . $image : '';
    }

}
