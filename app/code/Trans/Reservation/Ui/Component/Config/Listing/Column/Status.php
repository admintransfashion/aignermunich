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

namespace Trans\Reservation\Ui\Component\Config\Listing\Column;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Trans\Reservation\Api\Data\ReservationConfigInterface;

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
				$flag = (int)$item["flag"];

                if($flag === ReservationConfigInterface::FLAG_ACTIVE) {
                    $flag = __('Active');
                }

                if($flag === ReservationConfigInterface::FLAG_INACTIVE) {
                    $flag = __('Inactive');
                }

                $item[$this->getData('name')] = ucfirst($flag);

            }
        }
        return $dataSource;
    }
}
