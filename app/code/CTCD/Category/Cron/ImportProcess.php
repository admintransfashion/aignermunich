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
 * Class ImportProcess
 */
class ImportProcess
{
    /**
     * @var \CTCD\Category\Api\ImporterInterface
     */
    protected $importer;

    /**
     * @var \CTCD\Category\Model\ResourceModel\CategoryImport\CollectionFactory
     */
    protected $importCollection;

    /**
     * @var \CTCD\Category\Model\ResourceModel\CategoryImportData\CollectionFactory
     */
    protected $importDataCollection;

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
     * @param \CTCD\Category\Model\ResourceModel\CategoryImport\CollectionFactory $importCollection
     * @param \CTCD\Category\Model\ResourceModel\CategoryImportData\CollectionFactory $importDataCollection
     * @param \CTCD\Category\Api\CategoryImportRepositoryInterface $importRepository
     * @param \CTCD\Category\Api\CategoryImportDataRepositoryInterface $dataRepository
     * @param \CTCD\Category\Helper\Config $configHelper
     */
    public function __construct(
        \CTCD\Category\Api\ImporterInterface $importer,
        \CTCD\Category\Model\ResourceModel\CategoryImport\CollectionFactory $importCollection,
        \CTCD\Category\Model\ResourceModel\CategoryImportData\CollectionFactory $importDataCollection,
        \CTCD\Category\Api\CategoryImportRepositoryInterface $importRepository,
        \CTCD\Category\Api\CategoryImportDataRepositoryInterface $dataRepository,
        \CTCD\Category\Helper\Config $configHelper
    ) {
        $this->importer = $importer;
        $this->importCollection = $importCollection;
        $this->importDataCollection = $importDataCollection;
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
        if($this->configHelper->isEnabled()){
            $import = $this->initImport();

            if ($import) {
                $this->importer->importData($import);
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
        $import = $this->importCollection->create();
        $import->addFieldToFilter(
            array('status', 'status'),
            array(CategoryImportInterface::FLAG_PENDING, CategoryImportInterface::FLAG_PROGRESS)
        );

        $import->setOrder('entity_id', 'ASC');
        $import->setPageSize(1);
        $data = $import->getFirstItem();


        if ($import->getSize()) {
            return $data;
        }

        return null;
    }
}
