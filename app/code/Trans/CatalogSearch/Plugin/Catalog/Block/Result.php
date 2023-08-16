<?php
/**
 * @category Trans
 * @package  Trans_CatalogSeacrh
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\CatalogSearch\Plugin\Catalog\Block;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\CatalogSearch\Helper\Data;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\QueryFactory;

/**
 * Product search result block
 *
 * @api
 * @since 100.0.2
 */
class Result extends Template
{
    /**
     * Catalog Product collection
     *
     * @var Collection
     */
    protected $productCollection;

    /**
     * Catalog search data
     *
     * @var Data
     */
    protected $catalogSearchData;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param Data $catalogSearchData
     * @param QueryFactory $queryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        Data $catalogSearchData,
        QueryFactory $queryFactory,
        array $data = []
    ) {
        $this->catalogLayer = $layerResolver->get();
        $this->catalogSearchData = $catalogSearchData;
        $this->queryFactory = $queryFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get search query text
     *
     * @return \Magento\Framework\Phrase
     */
    public function aroundGetSearchQueryText(\Magento\CatalogSearch\Block\Result $subject)
    {
        $qCount = $subject->getResultCount();
        
        if ($qCount) {
            return __('Your Result For <b>"%1"</b>', $this->catalogSearchData->getEscapedQueryText());
        }
        else {
            return __('Sorry, We Couldn\'t find any result matching <b>"%1"</b>', $this->catalogSearchData->getEscapedQueryText());
        }
        
    }
}
