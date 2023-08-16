<?php
/**
 * @category Trans
 * @package  Trans_ProductRelation
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Preference\Catalog\Block\Product\View;

use Magento\Catalog\Block\Product\View\Details as CatalogDetail;

/**
 * Product details block.
 */
class Details extends CatalogDetail
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve product object
     *
     * @return Product
     * @throws \LogicExceptions
     */
    public function getProduct()
    {
        if (!$this->product) {
            if ($this->registry->registry('current_product')) {
                $this->product = $this->registry->registry('current_product');
            } else {
                throw new \LogicException('Product is not defined');
            }
        }
        return $this->product;
    }

    /**
     * Set product object
     *
     * @param Product $product
     * @return \Magento\Catalog\Block\Product\View\Details
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * get description image
     * 
     * @return string
     */
    public function getDescriptionImage()
    {
        $descImage = false;
        $product = $this->getProduct();
        $productimages = $product->getMediaGalleryImages();
        
        foreach($productimages as $productimage)
        {
            $descImage = $productimage['url'];
        }

        if(!$descImage) {
            return false;
        }
        
        return $descImage;
    }
}
