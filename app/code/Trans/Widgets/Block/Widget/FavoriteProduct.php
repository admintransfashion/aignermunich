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
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Widgets\Block\Widget;

class FavoriteProduct extends \Trans\Widgets\Block\Widget\AbstractWidget
{
    protected $_template = 'widget/favoriteproduct.phtml';

    /**
     * \Magento\Catalog\Block\Product\ImageBuilder
     */
    private $imageBuilder;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Trans\Gtm\Helper\Data
     */
    protected $gtmHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \CTCD\Core\Helper\Url $ctcdCoreUrlHelper
     * @param \Trans\Gtm\Helper\Data $gtmHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \CTCD\Core\Helper\Url $ctcdCoreUrlHelper,
        \Trans\Gtm\Helper\Data $gtmHelper,
        array $data = []
    ) {
        $this->imageBuilder = $imageBuilder;
        $this->productRepository = $productRepository;
        $this->customerSession = $customerSession;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->gtmHelper = $gtmHelper;
        parent::__construct($context, $ctcdCoreUrlHelper, $data);
    }

    /**
     * Get Media URL
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->ctcdCoreUrlHelper->getMediaUrl();
    }

    /**
     * Get Image url based on ID
     *
     * @param int $imageId
     * @return array|mixed|string
     */
    public function getBannerImage(int $imageId)
    {
        $imageUrl = '';
        if($imageId && is_int($imageId) &&  (int) $imageId > 0){
            $imageId = 'bannerimg' . $imageId;
            if($imageUrl = $this->getData($imageId)){
                if(strpos($imageUrl, $this->getMediaUrl()) === false){
                    $imageUrl = $this->getMediaUrl() . $imageUrl;
                }
            }
            else{
                $imageUrl = $this->getMediaUrl().'aigner/noimage.png';
            }
        }
        return $imageUrl;
    }

    /**
     * Get Product by SKU
     * @param mixed
     * @return \Magento\Catalog\Model\Product $product
     */
    public function getProductBySKU($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
        } catch (\Exception $e) {
            $product = null;
        }
        return $product;
    }

    /**
     * Get Product by SKU
     * @param string $skus
     * @return string Html Product
     */
    public function generateProduct($skus)
    {
        if(empty($skus)){
            return $skus;
        }

        $productSkus = explode(',',$skus);
        $i = 0;
        $result = "";

        $getCustomerId = $this->gtmHelper->getCurrentCustomerId();

        foreach($productSkus as $sku){
            if( $i > 2){
                break;
            }
            $product = $this->getProductBySKU($sku);
            if($product && $product->getId()){
                $imageUrl = $this->getImage($product, 'product_favorite_image_widget')->getImageUrl();

                $productSize = $this->gtmHelper->getProductAttributeValue($product->getId(), 'size');
                $productColor = $this->gtmHelper->getProductAttributeValue($product->getId(), 'color');
                $productFor = $this->gtmHelper->getCategoryC0NamesByProduct($product);
                $productCategory = $this->gtmHelper->getCategoryNamesByProduct($product);

                $onClickDatalayer = 'dataLayer.push({\'event\': \'product_click_favorites\',\'product_size\': \''.$productSize.'\',\'product_for\': \''.$productFor.'\',\'user_id\': \''.$getCustomerId.'\',\'ecommerce\': {\'click\': {\'actionField\': {\'list\': \'Favorites\'},\'products\': [{\'name\': \''.$product->getName().'\',\'id\': \''.$product->getSku().'\',\'price\': \''.$product->getFinalPrice().'\',\'brand\': \'Aigner\',\'category\': \''.$productCategory.'\',\'variant\': \''.$productColor.'\',\'position\': \''. ($i+1).'\'}]}}});';
                $onViewDatalayer = 'dataLayer.push({\'event\': \'product_view_favorites\',\'product_size\': \''.$productSize.'\',\'product_for\': \''.$productFor.'\',\'user_id\': \''.$getCustomerId.'\',\'ecommerce\': {\'currencyCode\': \'IDR\',\'impressions\': [{\'name\': \''.$product->getName().'\',\'id\': \''.$product->getSku().'\',\'price\': \''.$product->getFinalPrice().'\',\'brand\': \'Aigner\',\'category\': \''.$productCategory.'\',\'variant\': \''.$productColor.'\',\'list\': \'Favorites\',\'position\': \''.($i+1).'\'}]}});';

                $result .= ($i==1) ? '<div class="favorites-product middle">' : '<div class="favorites-product">';

                $result .= '<a onclick="'.$onClickDatalayer.'" href="'.$product->getProductUrl().'" class="favorites-link" id="productFavorites'.($i+1).'" data-layer-already="0">';
                $result .= '<img src="'.$imageUrl.'">';
                $result .= '<span class="favorites-name">'.$product->getName().'</span> </a>';
                $result .= '<span class="datalayer-click" style="display:none" onclick="'. $onViewDatalayer .'"></span>';
                $result .= '</div>';

                $i++;
            }
        }
        return $result ;
    }

    /**
     * get customer id
     * @return string
     */
    public function getCustomerIdCustom()
    {
        //return current customer ID
        if ($this->customerSession->getCustomer()->getEmail()) {
            $hashcustomeremail = hash('sha256', $this->customerSession->getCustomer()->getEmail());
            return $hashcustomeremail;
        }
        return false;
    }

    /**
     * Get category collection
     *
     * @param bool $isActive
     * @param bool|int $level
     * @param bool|string $sortBy
     * @param bool|int $pageSize
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');

        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }

        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }

        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }

        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }

        return $collection;
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
