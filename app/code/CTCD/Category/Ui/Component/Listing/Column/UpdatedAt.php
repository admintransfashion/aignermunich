<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Ui\Component\Listing\Column;


use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class UpdatedAt extends Column
{

    /**
     * @var \CTCD\Core\Helper\DateTime
     */
    protected $coreDateTimeHelper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \CTCD\Core\Helper\DateTime $coreDateTimeHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \CTCD\Core\Helper\DateTime $coreDateTimeHelper,
        array $components = [],
        array $data = []
    ){
        $this->coreDateTimeHelper = $coreDateTimeHelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['updated_at'])) {
                    $item[$this->getData('name')] = $this->coreDateTimeHelper->convertUTCZeroToCurrentTimezone($item['updated_at']);
                }
            }
        }
        return $dataSource;
    }
}
