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

namespace CTCD\ProductList\Observer;

use Magento\Framework\Data\Tree\Node;

class Topmenu implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \CTCD\ProductList\Helper\Data
     */
    protected $productlistHelper;

    /**
     * @param \CTCD\ProductList\Helper\Data $productlistHelper
     */
    public function __construct(
        \CTCD\ProductList\Helper\Data $productlistHelper
    ){
        $this->productlistHelper  = $productlistHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->productlistHelper->isModuleEnabled()) {

            /** @var \Magento\Framework\Data\Tree\Node $parentNode */
            $parentNode = $observer->getMenu();

            $productList = [];
            $parentURL = '/' . \CTCD\ProductList\Block\Product\ProductList::DEFAULT_PARENT_URL . '/';
            if($this->productlistHelper->isNewProductEnabled()){
                $productList[$this->productlistHelper->getNewProductOrderMenu()] = [
                    'key' => 'new-in',
                    'url' => $parentURL . \CTCD\ProductList\Block\Product\NewProduct::DEFAULT_CHILD_URL,
                    'title' => $this->productlistHelper->getNewProductTitle()
                ];
            }
            if($this->productlistHelper->isBestsellerEnabled()){
                if(isset($productList[$this->productlistHelper->getBestsellerOrderMenu()])){
                    end($productList);
                    $key = key($productList);
                    $productList[$key + 1] = [
                        'key' => 'best-seller',
                        'url' => $parentURL . \CTCD\ProductList\Block\Product\Bestseller::DEFAULT_CHILD_URL,
                        'title' => $this->productlistHelper->getBestsellerTitle()
                    ];
                }
                else{
                    $productList[$this->productlistHelper->getBestsellerOrderMenu()] = [
                        'key' => 'best-seller',
                        'url' => $parentURL . \CTCD\ProductList\Block\Product\Bestseller::DEFAULT_CHILD_URL,
                        'title' => $this->productlistHelper->getBestsellerTitle()
                    ];
                }
            }
            if($this->productlistHelper->isSaleProductEnabled()){
                if(isset($productList[$this->productlistHelper->getSaleProductOrderMenu()])){
                    end($productList);
                    $key = key($productList);
                    $productList[$key + 1] = [
                        'key' => 'sale',
                        'url' => $parentURL . \CTCD\ProductList\Block\Product\SaleProduct::DEFAULT_CHILD_URL,
                        'title' => $this->productlistHelper->getSaleProductTitle()
                    ];
                }
                else{
                    $productList[$this->productlistHelper->getSaleProductOrderMenu()] = [
                        'key' => 'sale',
                        'url' => $parentURL . \CTCD\ProductList\Block\Product\SaleProduct::DEFAULT_CHILD_URL,
                        'title' => $this->productlistHelper->getSaleProductTitle()
                    ];
                }
            }

            if(!empty($productList)){
                ksort($productList);
                foreach($productList as $page){
                    $data = [
                        'name' => __($page['title']),
                        'id' => 'product-'.$page['key'],
                        'url' => $page['url'],
                        'is_active' => true
                    ];

                    $inspirationNode = new Node($data, 'id', $parentNode->getTree(), $parentNode);
                    $parentNode->addChild($inspirationNode);
                }
            }
        }

        return $this;
    }
}
