<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Reservation
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Reservation\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Constant config path
     */
    const ENABLE_MODULE = 'reservation/general/enabled';
    const DISABLE_SALES = 'reservation/general/disable_sales';
    const LIMIT_PERDAY = 'reservation/general/reservation_limit_perday';
    const GUEST_ADD_TO_CART = 'reservation/general/guest_add_to_cart';
    const NUMBER_DIGIT = 'reservation/reservation_number/reservation_number_digit';
    const NUMBER_CODE = 'reservation/reservation_number/reservation_number_code';
    const STORECODE_PREFIX = 'reservation/reservation_number/use_stocecode_prefix';
    const DELIMITER = 'reservation/reservation_number/reservation_number_delimiter';
    const PRODUCT_BUFFER = 'reservation/product/buffer';
    const MAX_ITEM_QTY = 'reservation/product/max_item_qty';
    const ENABLE_QTY_ITEM = 'reservation/product/enable_qty_per_item';
    const HOURS_PRIORITY = 'reservation/product/hours_priority';
    const BUFFER_PRIORITY = 'reservation/product/buffer_priority';
    const DATE_PRIORITY = 'reservation/product/date_priority';
    const MAXQTY_PRIORITY = 'reservation/product/maxqty_priority';
    const FILTER_PRIORITY = 'reservation/product/filter_priority';
    const ENABLE_MAX_QTY = 'reservation/product/enable_max_qty';
    const PRIORITY = 'reservation/product/priority';
    const DIFF_STORE = 'reservation/product/multi_store';
    const ENABLE_SELECT_DATE = 'reservation/reservation_rules/enable_select_date';
    const ENABLE_SELECT_TIME = 'reservation/reservation_rules/enable_select_time';
    const ENABLE_EXPIRE_NEXTDAY = 'reservation/reservation_rules/enable_expire_nextday';
    const EXPIRE_TIME = 'reservation/reservation_rules/expire_time';
    const DEFAULT_QTY_ITEM = 'reservation/product/default_qty';

    /**
     * Get config value by path
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * is module enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfigValue(self::ENABLE_MODULE);
    }

    /**
     * is expire nextd day
     *
     * @return bool
     */
    public function isExpireNextDay()
    {
        return $this->getConfigValue(self::ENABLE_EXPIRE_NEXTDAY);
    }

    /**
     * is sales disabled
     *
     * @return bool
     */
    public function isSalesDisabled()
    {
        return $this->getConfigValue(self::DISABLE_SALES);
    }

    /**
     * is sales disabled
     *
     * @return bool
     */
    public function isGuestAddToCart()
    {
        return $this->getConfigValue(self::GUEST_ADD_TO_CART);
    }

    /**
     * is enable multi store
     *
     * @return bool
     */
    public function isEnableMultiStore()
    {
        return $this->getConfigValue(self::DISABLE_SALES);
    }

    /**
     * is enable max qty config
     *
     * @return bool
     */
    public function isEnableMaxQtyConfig()
    {
        return $this->getConfigValue(self::ENABLE_MAX_QTY);
    }

    /**
     * is store code use as prefix
     *
     * @return bool
     */
    public function isStorecodePrefix()
    {
        return $this->getConfigValue(self::STORECODE_PREFIX);
    }

    /**
     * get expire time
     *
     * @return string
     */
    public function getExpireTime()
    {
        return $this->getConfigValue(self::EXPIRE_TIME);
    }

    /**
     * get default qty
     *
     * @return int
     */
    public function getDefaultQty()
    {
        return $this->getConfigValue(self::DEFAULT_QTY_ITEM);
    }

    /**
     * reservation number delimiter
     *
     * @return bool
     */
    public function getReservationNumberDelimiter()
    {
        return $this->getConfigValue(self::DELIMITER);
    }

    /**
     * product buffer
     *
     * @return int
     */
    public function getGlobalProductBuffer()
    {
        return $this->getConfigValue(self::PRODUCT_BUFFER);
    }

    /**
     * filter priority
     *
     * @return string
     */
    public function getFilterPriority()
    {
        return $this->getConfigValue(self::FILTER_PRIORITY);
    }

    /**
     * config priority
     *
     * @return string
     */
    public function getConfigPriority()
    {
        return $this->getConfigValue(self::PRIORITY);
    }

    /**
     * filter priority
     *
     * @return int
     */
    public function getDatePriority()
    {
        return $this->getConfigValue(self::DATE_PRIORITY);
    }

    /**
     * filter priority
     *
     * @return int
     */
    public function getHoursPriority()
    {
        return $this->getConfigValue(self::HOURS_PRIORITY);
    }

    /**
     * buffer priority
     *
     * @return int
     */
    public function getBufferPriority()
    {
        return $this->getConfigValue(self::BUFFER_PRIORITY);
    }

    /**
     * max qty priority
     *
     * @return int
     */
    public function getMaxqtyPriority()
    {
        return $this->getConfigValue(self::MAXQTY_PRIORITY);
    }

    /**
     * get reservation limit per day
     *
     * @return int
     */
    public function getReservationLimit()
    {
        return $this->getConfigValue(self::LIMIT_PERDAY);
    }

    /**
     * Max item qty
     *
     * @return int
     */
    public function getMaxQty()
    {
        return $this->getConfigValue(self::MAX_ITEM_QTY);
    }

    /**
     * Reservation Number Digit
     *
     * @return int
     */
    public function getReservationNumberDigit()
    {
        return $this->getConfigValue(self::NUMBER_DIGIT);
    }

    /**
     * Reservation Number Code
     *
     * @return string
     */
    public function getReservationNumberCode()
    {
        return !empty($this->getConfigValue(self::NUMBER_CODE)) ? strtoupper($this->getConfigValue(self::NUMBER_CODE)) : null;
    }

    /**
     * is qty configuration for item
     *
     * @return bool
     */
    public function isQtyForItem()
    {
        return $this->getConfigValue(self::ENABLE_QTY_ITEM);
    }

    /**
     * get add to cart template
     *
     * @return string
     */
    public function getAddtocartTemplate()
    {
        $template = 'Magento_Catalog::product/view/addtocart.phtml';

        if($this->isSalesDisabled()) {
            $template = 'Trans_Reservation::product/view/addtocart.phtml';
        }

        return $template;
    }

    /**
     * get add to cart template
     *
     * @return string
     */
    public function getProductListTemplate()
    {
        $template = 'Magento_Catalog::product/list.phtml';

        if($this->isSalesDisabled()) {
            $template = 'Trans_Reservation::product/list.phtml';
        }

        return $template;
    }
}
