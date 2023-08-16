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

class EditorialFeaturedProduct extends \Trans\Widgets\Block\Widget\AbstractWidget
{
    const DEFAULT_BUTTON_TITLE = 'SHOW ALL PRODUCTS';
    const MAX_PRODUCT = 3;

    protected $_template = 'widget/editorialfeaturedproduct.phtml';

    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \CTCD\Core\Helper\Url $ctcdCoreUrlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \CTCD\Core\Helper\Url $ctcdCoreUrlHelper,
        array $data = []
    ) {
        $this->imageBuilder = $imageBuilder;
        $this->productRepository = $productRepository;
        parent::__construct($context, $ctcdCoreUrlHelper, $data);
    }

    /**
     * Get product collection
     * @return \Magento\Catalog\Model\Product[]|null
     */
    public function getProductCollection()
    {
        $collection = null;
        $skus = null;

        if($products = $this->getData('products')){
            $skus = array_filter(array_map('trim', explode(',', $products)));
        }

        if(is_array($skus) && !empty($skus)){
            $counter = 0;
            foreach ($skus as $sku) {
                if($product = $this->getProductBySKU($sku)){
                    $collection[$sku] = $product;
                    if($counter >= (self::MAX_PRODUCT - 1)){
                        break;
                    }
                    $counter++;
                }
            }
        }
        return $collection;
    }

    /**
     * Get Product by SKU
     * @param string $sku
     * @return \Magento\Catalog\Model\Product $product
     */
    protected function getProductBySKU($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
        } catch (\Exception $exception) {
            $product = null;
        }
        return $product;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string|null
     */
    public function getProductUrl(\Magento\Catalog\Model\Product $product)
    {
        $url = null;
        if($product && $product->getId()){
            $url = $product->getUrlModel()->getUrl($product);
        }
        return $url;
    }

    /**
     * Get product price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
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
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->create($product, $imageId, $attributes);
    }
}

