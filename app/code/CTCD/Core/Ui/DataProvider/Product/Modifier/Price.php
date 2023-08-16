<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Core\Ui\DataProvider\Product\Modifier;

class Price extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    private $locator;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \CTCD\Core\Helper\Data
     */
    private $ctcdCoreHelper;

    /**
     * @param \Magento\Catalog\Model\Locator\LocatorInterface $locator
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \CTCD\Core\Helper\Data $ctcdCoreHelper
     */
    public function __construct(
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \CTCD\Core\Helper\Data $ctcdCoreHelper
    ) {

        $this->locator = $locator;
        $this->dataPersistor = $dataPersistor;
        $this->ctcdCoreHelper = $ctcdCoreHelper;
    }

    public function modifyData( array $data )
    {
        if (!$this->locator->getProduct()->getId() && $this->dataPersistor->get('catalog_product')) {
            return $this->resolvePersistentData($data);
        }
        $productId = $this->locator->getProduct()->getId();
        $productPrice =  $this->locator->getProduct()->getPrice();
        $data[$productId][self::DATA_SOURCE_DEFAULT]['price'] = $this->formatPrice($productPrice);
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta( array $meta )
    {
        return $meta;
    }

    /**
     * Format price to have only two decimals after delimiter
     *
     * @param mixed $value
     * @return string
     * @since 101.0.0
     */
    protected function formatPrice($value)
    {
        $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION;
        if ($this->ctcdCoreHelper->isDecimalPriceFeatureEnabled()) {
            $precision = $this->ctcdCoreHelper->isShowDecimalOnPrice() ? $this->ctcdCoreHelper->getDecimalPricePrecision() : 0;
        }
        return $value !== null ? number_format((float)$value, $precision, '.', '') : '';
    }

    /**
     * Resolve data persistence
     *
     * @param array $data
     * @return array
     */
    private function resolvePersistentData(array $data)
    {
        $persistentData = (array)$this->dataPersistor->get('catalog_product');
        $this->dataPersistor->clear('catalog_product');
        $productId = $this->locator->getProduct()->getId();

        if (empty($data[$productId][self::DATA_SOURCE_DEFAULT])) {
            $data[$productId][self::DATA_SOURCE_DEFAULT] = [];
        }

        $data[$productId] = array_replace_recursive(
            $data[$productId][self::DATA_SOURCE_DEFAULT],
            $persistentData
        );

        return $data;
    }
}
