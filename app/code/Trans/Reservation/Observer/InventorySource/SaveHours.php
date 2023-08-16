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

namespace Trans\Reservation\Observer\InventorySource;

use Magento\Framework\Event\ObserverInterface;
use Trans\Reservation\Api\Data\SourceAttributeInterface;

/**
 * Class SaveHours
 */
class SaveHours implements ObserverInterface
{
    /**
     * @var \Trans\Reservation\Api\Data\SourceAttributeInterfaceFactory
     */
    protected $sourceAttrFactory;

    /**
     * @var \Trans\Reservation\Api\SourceAttributeRepositoryInterface
     */
    protected $sourceAttrRepository;

    /**
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterfaceFactory $sourceAttrFactory
     * @param \Trans\Reservation\Api\SourceAttributeRepositoryInterface $sourceAttrRepository
     */
    public function __construct(
        \Trans\Reservation\Api\Data\SourceAttributeInterfaceFactory $sourceAttrFactory,
        \Trans\Reservation\Api\SourceAttributeRepositoryInterface $sourceAttrRepository
    )
    {
        $this->sourceAttrFactory = $sourceAttrFactory;
        $this->sourceAttrRepository = $sourceAttrRepository;
    }

    /**
     * save source hours
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $source = $observer->getEvent()->getSource();
        $request = $observer->getEvent()->getRequest()->getParams();

        if(isset($request['general']['hour_open']) && isset($request['general']['hour_close'])) {
            $openHour = $request['general']['hour_open'];
            $closeHour = $request['general']['hour_close'];

            $code = $source->getSourceCode();

            $data['sourceCode'] = $code;
            $data['attribute'][SourceAttributeInterface::OPEN_HOUR_ATTR] = $openHour;
            $data['attribute'][SourceAttributeInterface::CLOSE_HOUR_ATTR] = $closeHour;

            $this->sourceAttrRepository->saveData($data);
        }

        if(isset($request['general']['district']) && isset($request['general']['district'])) {
            $district = $request['general']['district'];

            $code = $source->getSourceCode();

            $data['sourceCode'] = $code;
            $data['attribute'][SourceAttributeInterface::DISTRICT_ATTR] = $district;

            $this->sourceAttrRepository->saveData($data);
        }

        return $this;
    }
}
