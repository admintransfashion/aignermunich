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

namespace Trans\Reservation\Controller\Adminhtml;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Downloader
 */
class Downloader extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\ImportExport\Model\Import\SampleFileProvider
     */
    protected $sampleFileProvider;

    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\ImportExport\Model\Import\SampleFileProvider $sampleFileProvider
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\ImportExport\Model\Import\SampleFileProvider $sampleFileProvider
    ) {
        $this->fileFactory = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->sampleFileProvider = $sampleFileProvider;

        parent::__construct($context);
    }

    public function execute()
    {

    }

    /**
     * Download sample file action
     *
     * @param string $entityName
     * @param string $ext
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function download(string $entityName, string $ext = 'csv')
    {
        try {
            $fileContents = $this->sampleFileProvider->getFileContents($entityName);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('There is no sample file for this entity.'));
            return false;
        }

        $fileSize = $this->sampleFileProvider->getSize($entityName);
        $fileName = $entityName . '.' . $ext;

        $this->fileFactory->create(
            $fileName,
            null,
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($fileContents);
        return $resultRaw;
    }
}
