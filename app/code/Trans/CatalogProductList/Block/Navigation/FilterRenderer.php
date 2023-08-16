<?php

/**
 * @category Trans
 * @package  Trans_CatalogProductList
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CatalogProductList\Block\Navigation;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;

/**
 * Class FilterRenderer
 */
class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Trans\Gtm\Helper\Data
     */
    protected $gtmHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Trans\Gtm\Helper\Data $gtmHelper
     * @param array $data
     */
    function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Trans\Gtm\Helper\Data $gtmHelper,
        array $data = []
    ){
        $this->customerSession = $customerSession;
        $this->gtmHelper = $gtmHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function render(FilterInterface $filter) {
        $this->assign('filterItems', $filter->getItems());
        $this->assign('filter', $filter);
        $html = $this->_toHtml();
        $this->assign('filterItems', []);
        return $html;
    }

    /**
     * Price Range on Frontend Storefront
     */
    public function getPriceRange($filter) {
        $filterprice = array('min' => 0, 'max' => 0);
        $priceArr = $filter->getResource()->loadPrices(10000000000);
        $filterprice['min'] = reset($priceArr);
        $filterprice['max'] = end($priceArr);
        return $filterprice;
    }

    /**
     * Redirect After Filtering Price
     */
    public function getFilterUrl($filter) {
        $query = ['price' => ''];
        return $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

     /**
     * get customer id
     * @return string
     */
    public function getCurrentCustomerId()
    {
        return $this->gtmHelper->getCurrentCustomerId();
    }
}
