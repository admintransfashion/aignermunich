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

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use CTCD\ProductList\Helper\Data as ProductListHelper;

/**
 * Class ProductList
 * @package CTCD\ProductList\Block\Product
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ProductList extends AbstractProduct
{
    const DEFAULT_PARENT_URL = 'products';

    /**
     * @var AbstractCollection
     */
    protected $productCollection;

    /**
     * @var ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductListHelper
     */
    protected $productListHelper;

    public function __construct(
        Context $context,
        ImageBuilder $imageBuilder,
        CollectionFactory $productCollectionFactory,
        ProductListHelper $productListHelper,
        array $data = []
    ) {
        $this->imageBuilder = $imageBuilder;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productListHelper = $productListHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return AbstractCollection
     */
    protected function getProductCollection()
    {
        if ($this->productCollection === null) {
            $this->productCollection = $this->initializeProductCollection();
        }

        return $this->productCollection;
    }

    /**

     * @return Collection
     */
    protected function initializeProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $this->addToolbarBlock($collection);
        return $collection;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {
        return $this->getProductCollection();
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getProductCollection();

        $this->addToolbarBlock($collection);

        if (!$collection->isLoaded()) {
            $collection->load();
        }

        return parent::_beforeToHtml();
    }

    /**
     * Add toolbar block from product listing layout
     *
     * @param Collection $collection
     */
    protected function addToolbarBlock(Collection $collection)
    {
        $toolbarLayout = $this->getToolbarFromLayout();

        if ($toolbarLayout) {
            $this->configureToolbar($toolbarLayout, $collection);
        }
    }

    /**
     * Get toolbar block from layout
     *
     * @return bool|Toolbar
     */
    private function getToolbarFromLayout()
    {
        $blockName = $this->getToolbarBlockName();

        $toolbarLayout = false;

        if ($blockName) {
            $toolbarLayout = $this->getLayout()->getBlock($blockName);
        }

        return $toolbarLayout;
    }

    /**
     * @param Toolbar $toolbar
     * @param Collection $collection
     * @return void
     */
    private function configureToolbar(Toolbar $toolbar, Collection $collection)
    {
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Set collection.
     *
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->productCollection = $collection;
        return $this;
    }

    /**
     * Get product price
     *
     * @param Product $product
     * @return string
     */
    public function getProductPrice(Product $product)
    {
        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $priceType = \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE;
        $arguments['zone'] = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST;
        $price = '';

        if ($priceRender) {
            $price = $priceRender->render($priceType, $product, $arguments);
        }

        return $price;
    }

    /**
     * Retrieve product image
     *
     * @param Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->create($product, $imageId, $attributes);
    }


}
