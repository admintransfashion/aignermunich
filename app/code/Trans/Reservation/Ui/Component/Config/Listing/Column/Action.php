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
 * Class Action
 */
class Action extends Column
{
    /**
     * @var \Magento\Framework\Url
     */
	protected $urlBuilder;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Url $urlBuilder
     * @param \Magento\Framework\Escaper $escaper
     */
	public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
		\Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        array $components = [], array $data = [])
    {
		$this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;

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
                $config = $item[ReservationConfigInterface::CONFIG];

				$viewUrl = $this->urlBuilder->getUrl('reservation/' . $config . '/edit', ['id' => $item['id']]);
                $deleteUrl = $this->urlBuilder->getUrl('reservation/' . $config . '/delete', ['id' => $item['id']]);

                $title = $this->escaper->escapeHtml($item['title']);

				$item[$this->getData('name')] = [
                    'view' => [
                        'href' => $viewUrl,
                        'label' => __('Edit'),
                    ],
                    'delete' => [
                        'href' => $deleteUrl,
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete %1', $title),
                            'message' => __('Are you sure you want to delete a %1 record?', $title)
                        ]
                    ]
                ];
            }
        }
        return $dataSource;
    }
}
