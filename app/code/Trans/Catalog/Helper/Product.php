<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Catalog
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Catalog\Helper;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    const BADGE_SALE = 'sale';
    const BADGE_BESTSELLER = 'bestseller';
    const BADGE_NEW = 'new';
    const PRODUCT_BADGE_PRIORITY = [self::BADGE_SALE, self::BADGE_BESTSELLER, self::BADGE_NEW];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get product badge html
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductBadge(\Magento\Catalog\Model\Product $product)
    {
        $badgeHtml = '';
        $appliedBadges = [];

        /**
         * Sale Badge
         */
        $regularPrice = $product->getPrice();
        $finalPrice =  $product->getFinalPrice();
        if($finalPrice < $regularPrice){
            $appliedBadges[] = self::BADGE_SALE;
        }

        $storeId = $this->storeManager->getStore()->getId();

        /**
         * Bestseller Badge
         */
        $bestsellerProduct = $product->getResource()->getAttributeRawValue($product->getId(),'is_bestseller', $storeId);
        if($bestsellerProduct){
            $appliedBadges[] = self::BADGE_BESTSELLER;
        }

        /**
         * New Badge
         */
        $newProduct = $product->getResource()->getAttributeRawValue($product->getId(),'is_new', $storeId);
        if($newProduct){
            $appliedBadges[] = self::BADGE_NEW;
        }

        if(!empty($appliedBadges)){
            foreach(self::PRODUCT_BADGE_PRIORITY as $badge){
                if(in_array($badge, $appliedBadges)){
                    $badgeHtml = $this->getBadgeHtml($badge);
                    break;
                }
            }
        }

        return $badgeHtml;
    }

    /**
     * Get badge html
     *
     * @param string $badge
     * @return string
     */
    public function getBadgeHtml($badge)
    {
        $html = '<div class="product-badge '.$badge.'"><span class="ng-binding">'.__($badge).'</span></div>';
        return $html;
    }
}
