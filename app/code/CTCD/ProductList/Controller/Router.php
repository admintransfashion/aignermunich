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

namespace CTCD\ProductList\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var \CTCD\ProductList\Helper\Data
     */
    protected $productlistHelper;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \CTCD\ProductList\Helper\Data $productlistHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \CTCD\ProductList\Helper\Data $productlistHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->productlistHelper = $productlistHelper;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if($this->productlistHelper->isModuleEnabled()){
            $identifier = trim($request->getPathInfo(), '/');
            $parentURL = \CTCD\ProductList\Block\Product\ProductList::DEFAULT_PARENT_URL . '/';
            if(strpos($identifier, $parentURL) !== false && substr_count($identifier, '/') == 1){
                $identifiers = explode('/', $identifier);
                $childUrl = end($identifiers);
                if($this->productlistHelper->isNewProductEnabled() && $childUrl == \CTCD\ProductList\Block\Product\NewProduct::DEFAULT_CHILD_URL) {
                    $request->setModuleName('customproductlist')->setControllerName('product')->setActionName('newproduct');
                    return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
                } elseif($this->productlistHelper->isBestsellerEnabled() && $childUrl == \CTCD\ProductList\Block\Product\Bestseller::DEFAULT_CHILD_URL) {
                    $request->setModuleName('customproductlist')->setControllerName('product')->setActionName('bestseller');
                    return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
                } elseif($this->productlistHelper->isSaleProductEnabled() && $childUrl == \CTCD\ProductList\Block\Product\SaleProduct::DEFAULT_CHILD_URL) {
                    $request->setModuleName('customproductlist')->setControllerName('product')->setActionName('saleproduct');
                    return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
                }
            }
        }

        return false;
    }
}
