<?php
/**
 * @category Trans
 * @package  Trans_Catalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Ui\Component\Listing\Column;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Api\SearchCriteriaBuilder;

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
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Url $urlBuilder
     */
	public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
		\Magento\Framework\UrlInterface $urlBuilder,
        array $components = [], array $data = [])
    {
		$this->urlBuilder = $urlBuilder;
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
				$editUrl = $this->urlBuilder->getUrl('catalog/season/form', ['id' => $item['id']]);
                
                if($item['code'] != 'all') {
    				$item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $editUrl,
                            'label' => __('Edit'),
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl('catalog/season/delete', ['id' => $item['id']]),
                            'label' => __('Delete'),
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}