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

namespace CTCD\ProductList\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const DEFAULT_TITLE_BESTSELLER = 'BEST SELLER';
    const DEFAULT_TITLE_NEW = 'NEW IN';
    const DEFAULT_TITLE_SALE = 'SALE';

    /**
     * @param string $field
     * @param string|null $scope
     * @param int|null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $scope = null, $storeId = null)
    {
        $scope = !$scope ? ScopeInterface::SCOPE_WEBSITE : $scope;
        return $this->scopeConfig->getValue(
            $field,
            $scope,
            $storeId
        );
    }

    /**
     * Is module enabled?
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return (bool) $this->getConfigValue('customproductlist/general/active');
    }

    /**
     * Is product displayed by badge?
     *
     * @return bool
     */
    public function isProductDisplayedByBadge()
    {
        return (bool) $this->getConfigValue('customproductlist/general/display_by_badge');
    }

    /**
     * Is new product page enabled?
     *
     * @return bool
     */
    public function isNewProductEnabled()
    {
        return (bool) $this->getConfigValue('customproductlist/newproduct/active');
    }

    /**
     * Get new product page order menu
     *
     * @return int
     */
    public function getNewProductOrderMenu()
    {
        return (int) $this->getConfigValue('customproductlist/newproduct/menu_order') ?: 0;
    }

    /**
     * Get new product page header title
     *
     * @return string
     */
    public function getNewProductTitle()
    {
        return $this->getConfigValue('customproductlist/newproduct/header_title') ?: self::DEFAULT_TITLE_NEW;
    }

    /**
     * Get new product page header image
     *
     * @return string
     */
    public function getNewProductImage()
    {
        return $this->getConfigValue('customproductlist/newproduct/header_image');
    }

    /**
     * Is bestseller page enabled?
     *
     * @return bool
     */
    public function isBestsellerEnabled()
    {
        return (bool) $this->getConfigValue('customproductlist/bestseller/active');
    }

    /**
     * Get bestseller page order menu
     *
     * @return int
     */
    public function getBestsellerOrderMenu()
    {
        return (int) $this->getConfigValue('customproductlist/bestseller/menu_order') ?: 1;
    }

    /**
     * Get bestseller page header title
     *
     * @return string
     */
    public function getBestsellerTitle()
    {
        return $this->getConfigValue('customproductlist/bestseller/header_title') ?: self::DEFAULT_TITLE_BESTSELLER;
    }

    /**
     * Get bestseller page header image
     *
     * @return string
     */
    public function getBestsellerImage()
    {
        return $this->getConfigValue('customproductlist/bestseller/header_image');
    }

    /**
     * Is sale product page enabled?
     *
     * @return bool
     */
    public function isSaleProductEnabled()
    {
        return (bool) $this->getConfigValue('customproductlist/saleproduct/active');
    }

    /**
     * Get sale product page order menu
     *
     * @return int
     */
    public function getSaleProductOrderMenu()
    {
        return (int) $this->getConfigValue('customproductlist/saleproduct/menu_order') ?: 1;
    }

    /**
     * Get sale product page header title
     *
     * @return string
     */
    public function getSaleProductTitle()
    {
        return $this->getConfigValue('customproductlist/saleproduct/header_title') ?: self::DEFAULT_TITLE_SALE;
    }

    /**
     * Get sale product page header image
     *
     * @return string
     */
    public function getSaleProductImage()
    {
        return $this->getConfigValue('customproductlist/saleproduct/header_image');
    }
}
