<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Cron;

use Magento\Framework\Exception\NoSuchEntityException;
use CTCD\Category\Api\Data\CategoryImportInterface;
use CTCD\Category\Api\Data\CategoryImportDataInterface;

/**
 * Class LateDataImport
 */
class LateDataImport
{
    /**
     * @var \CTCD\Category\Api\ImporterInterface
     */
    protected $importer;

    /**
     * @var \CTCD\Category\Model\ResourceModel\CategoryImportWait\CollectionFactory
     */
    protected $importWaitCollection;

    /**
     * @var \CTCD\Category\Api\CategoryImportRepositoryInterface
     */
    protected $importRepository;

    /**
     * @var \CTCD\Category\Api\CategoryImportDataRepositoryInterface
     */
    protected $dataRepository;

    /**
     * @var \CTCD\Category\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \CTCD\Category\Api\ImporterInterface $importer
     * @param \CTCD\Category\Model\ResourceModel\CategoryImportWait\CollectionFactory $waitImportCollection
     * @param \CTCD\Category\Api\CategoryImportRepositoryInterface $importRepository
     * @param \CTCD\Category\Api\CategoryImportDataRepositoryInterface $dataRepository
     * @param \CTCD\Category\Helper\Config $configHelper
     */
    public function __construct(
        \CTCD\Category\Api\ImporterInterface $importer,
        \CTCD\Category\Model\ResourceModel\CategoryImportWait\CollectionFactory $waitImportCollection,
        \CTCD\Category\Api\CategoryImportRepositoryInterface $importRepository,
        \CTCD\Category\Api\CategoryImportDataRepositoryInterface $dataRepository,
        \CTCD\Category\Helper\Config $configHelper
    ) {
        $this->importer = $importer;
        $this->waitImportCollection = $waitImportCollection;
        $this->importRepository = $importRepository;
        $this->dataRepository = $dataRepository;
        $this->configHelper = $configHelper;
    }

    /**
     * Execute
     *
     * @return json
     */
    public function execute()
    {
        if($this->configHelper->isEnabled()) {
            $import = $this->initImport();

            if ($import) {
                $this->importer->lateImportData($import);
            }
        }
    }

    /**
     * init Category import
     *
     * @return CTCD\Category\Model\ResourceModel\CategoryImport\Collection
     */
    protected function initImport()
    {
        $data = $this->waitImportCollection->create();
        $data->setPageSize(50);

        if($data->getSize()) {
            return $data;
        }

        return null;
    }
}
