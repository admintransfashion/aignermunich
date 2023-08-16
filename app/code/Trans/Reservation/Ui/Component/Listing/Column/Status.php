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

namespace Trans\Reservation\Ui\Component\Listing\Column;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Trans\Reservation\Api\Data\ReservationInterface;;
use Trans\Reservation\Api\Data\ReservationItemInterface;;

/**
 * Class Status
 */
class Status extends Column
{
    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationHelper;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Helper\Reservation $reservationHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Helper\Reservation $reservationHelper,
        array $components = [],
        array $data = []
    ) {
        $this->reservationRepository = $reservationRepository;
        $this->reservationHelper = $reservationHelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

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
				$reservation = $this->reservationRepository->getById($item["reservation_id"]);
                $refNumber = $item['reference_number'];

                switch ($reservation->getFlag()) {
                    case ReservationInterface::FLAG_SUBMIT:
                        $statusLabel = 'Waiting';
                        break;

                    default:
                        $statusLabel = $this->reservationHelper->getReservationBusinessStatus($reservation, $refNumber);
                        break;
                }


                // $flag = $item["flag"];

                // if($flag === ReservationInterface::FLAG_SUBMIT) {
                //     $flag = __('Waiting');
                // }

                // $item[$this->getData('name')] = ucfirst($flag);
                $item[$this->getData('name')] = ucwords($statusLabel);

            }
        }

        return $dataSource;
    }
}
