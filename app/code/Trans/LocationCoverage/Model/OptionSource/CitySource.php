<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Model\OptionSource;

// use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Trans\LocationCoverage\Model\ResourceModel\Collection\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Provide option values for UI
 *
 * @api
 */
class CitySource implements OptionSourceInterface
{
    /**
     * City collection factory
     *
     * @var CollectionFactory
     */
    private $cityCollectionFactory;

    /**
     * Source data
     *
     * @var null|array
     */
    private $sourceData;

    /**
     * @param CollectionFactory $cityCollectionFactory
     */
    public function __construct(CollectionFactory $cityCollectionFactory)
    {
        $this->cityCollectionFactory = $cityCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (null === $this->sourceData) {
            $cityCollection = $this->cityCollectionFactory->create();
            $this->sourceData = $cityCollection->toOptionArray();
        }
        return $this->sourceData;
    }
}
