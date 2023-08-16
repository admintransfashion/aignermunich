<?php
/**
 * @category Trans
 * @package  Trans_Catalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Model\Config\Source;

/**
 * Class Season
 */
class Season implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Trans\Catalog\Api\SeasonRepositoryInterface
     */
    protected $seasonRepository;
 
    /**
     * @param \Trans\Catalog\Model\ResourceModel\Season\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Trans\Catalog\Model\ResourceModel\Season\CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create();

        foreach($collection as $season) {
            $options[] = ['value' => $season->getCode(), 'label' => $season->getLabel()];    
        }

        return $options; 
    }
}
