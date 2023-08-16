<?php
/**
 * @category Trans
 * @package  Trans_CategoryImage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CategoryImage\Block;

use Magento\Catalog\Model\Product;

/**
 * Class Image
 */
class Image extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    protected $category = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Trans\CategoryImage\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Trans\CategoryImage\Helper\Category $categoryHelper,
        array $data = []
    )
    {
        $this->coreRegistry = $registry;
        $this->categoryHelper = $categoryHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current category model object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        if (!$this->category) {
            $this->category = $this->coreRegistry->registry('current_category');

            if (!$this->_category) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Category object could not be found in core registry'));
            }
        }
        return $this->category;
    }

    /**
     * Retrieve image url per category
     *
     */
    public function getImageUrl()
    {
        $imageCode = $this->hasImageCode() ? $this->getImageCode() : 'image';
        $image = $this->getCurrentCategory()->getData($imageCode);
        return $this->categoryHelper->getImageUrl($image);
    }

    public function getSortCat()
    {
        $catSort = $this->hasCategorySort() ? $this->getCategorySort() : 'sortcat';
        $sort = $this->getCurrentCategory()->getData($catSort);
        return $this->categoryHelper->getSortCat($sort);
    }
}