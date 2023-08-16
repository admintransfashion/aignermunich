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

namespace Trans\Reservation\Plugin\InventoryAdminUi\Ui\DataProvider;

use Trans\Reservation\Api\Data\SourceAttributeInterface;
use Magento\InventoryAdminUi\Ui\DataProvider\SourceDataProvider as MageSourceDataProvider;

/**
 * Class SourceDataProvider
 */
class SourceDataProvider
{
	/**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Trans\Reservation\Api\Data\SourceAttributeInterfaceFactory
     */
    protected $sourceAttrFactory;

    /**
     * @var \Trans\Reservation\Api\SourceAttributeRepositoryInterface
     */
    protected $sourceAttrRepository;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterfaceFactory $sourceAttrFactory
     * @param \Trans\Reservation\Api\SourceAttributeRepositoryInterface $sourceAttrRepository
     */
    public function __construct(
    	\Magento\Framework\App\Request\Http $request,
        \Trans\Reservation\Api\Data\SourceAttributeInterfaceFactory $sourceAttrFactory,
        \Trans\Reservation\Api\SourceAttributeRepositoryInterface $sourceAttrRepository
    )
    {
        $this->request = $request;
        $this->sourceAttrFactory = $sourceAttrFactory;
        $this->sourceAttrRepository = $sourceAttrRepository;
    }

    /**
     * prepare custom data for admin
     *
     * @param MageSourceDataProvider $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundGetData(MageSourceDataProvider $subject, callable $proceed)
    {
    	$result = $proceed();

    	if($this->request->getParam('source_code')) {
    		$sourceCode = $this->request->getParam('source_code');
    		$attrs = $this->sourceAttrRepository->getBySourceCode($sourceCode);

    		foreach($attrs as $attr) {
    			$result[$sourceCode]['general'][$attr->getAttribute()] = $attr->getValue();
    		}
    	}

    	return $result;
    }
}
