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
use Trans\Catalog\Api\Data\SeasonInterface;;
use Trans\Catalog\Api\Data\SeasonItemInterface;;

/**
 * Class Flag
 */
class Flag extends Column
{
    /**
     * @var \Trans\Catalog\Api\SeasonRepositoryInterface
     */
    protected $seasonRepository;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Trans\Catalog\Api\SeasonRepositoryInterface $seasonRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Trans\Catalog\Api\SeasonRepositoryInterface $seasonRepository,
        array $components = [],
        array $data = []
    ) {
        $this->seasonRepository = $seasonRepository;
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
				$flag = $item['flag'];
                $status = 'Inactive';

                if($flag) {
                    $status = 'Active';
                }

                $item[$this->getData('name')] = $status;
				
            }
        }

        return $dataSource;
    }
}