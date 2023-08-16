<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Ui\Component\Listing\Column;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Api\SearchCriteriaBuilder;
use CTCD\Category\Api\Data\CategoryImportInterface;

/**
 * Class Status
 */
class Status extends Column
{
    /**
     * Prepare customer column
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
				switch ($item['status']) {
                    case CategoryImportInterface::FLAG_PENDING:
                        $statusLabel = 'Pending';
                        break;

                    case CategoryImportInterface::FLAG_PROGRESS:
                        $statusLabel = 'Progress';
                        break;

                    case CategoryImportInterface::FLAG_FAIL:
                        $statusLabel = 'Failed';
                        break;

                    default:
                        $statusLabel = 'Success';
                        break;
                }

                $item[$this->getData('name')] = ucwords($statusLabel);
            }
        }

        return $dataSource;
    }
}
