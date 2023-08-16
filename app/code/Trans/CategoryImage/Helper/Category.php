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

namespace Trans\CategoryImage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Category
 */
class Category extends AbstractHelper
{
    /**
     * @return array
     */
    public function getAdditionalImageTypes()
    {
        return array('thumbnail');
    }

    public function getAdditionalCategorySort()
    {
        return array('sort_cat_order');
    }

    /**
     * Retrieve image URL
     * @param $image
     * @return string
     */
    public function getImageUrl($image)
    {
        $url = false;
        //$image = $this->getImage();
        if ($image) {
            if (is_string($image)) {
                $url = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . 'catalog/category/' . $image;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    public function getSortCat($sortCat)
    {
        return $sortCat;
    }

}