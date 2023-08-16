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

namespace Trans\CategoryImage\Controller\Adminhtml\Category;

/**
 * Class Save
 */
class Save extends \Magento\Catalog\Controller\Adminhtml\Category\Save
{

    /**
     * @return array
     */
    protected function getAdditionalImages() {
        return array('thumbnail');
    }

    /**
     * Image data preprocessing
     *
     * @param array $data
     *
     * @return array
     */
    public function imagePreprocessing($data)
    {
        foreach ($this->getAdditionalImages() as $imageType) {
            if (empty($data[$imageType])) {
                unset($data[$imageType]);
                $data[$imageType]['delete'] = true;
            }
        }
        return parent::imagePreprocessing($data);
    }

    /**
     * Filter category data
     *
     * @param array $rawData
     * @return array
     */
    protected function _filterCategoryPostData(array $rawData)
    {
        $data = $rawData;

        /**
         * a workaround for adding extra image fields to category form
         */

        foreach ($this->getAdditionalImages() as $imageType) {
            if (isset($data[$imageType]) && is_array($data[$imageType])) {
                if (!empty($data[$imageType]['delete'])) {
                    $data[$imageType] = null;
                } else {
                    if (isset($data[$imageType][0]['name']) && isset($data[$imageType][0]['tmp_name'])) {
                        $data[$imageType] = $data[$imageType][0]['name'];
                    } else {
                        unset($data[$imageType]);
                    }
                }
            }

        }

        return parent::_filterCategoryPostData($data);
    }
}
