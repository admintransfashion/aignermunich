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

/**
 * Class CustomerName
 */
class CustomerName extends Column
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
	protected $customerFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
	public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
        array $components = [], array $data = [])
    {
		$this->customerFactory = $customerFactory;
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
				$customer = $this->customerFactory->create()->load($item["customer_id"]);
				$item[$this->getData('name')] = $customer->getName();

            }
        }
        return $dataSource;
    }
}
