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

namespace CTCD\Category\Controller\Adminhtml\Import;

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
        $param = $this->getRequest()->getParams();

        if(isset($param['download'])) {
            $entityName = $param['download'];
        } else {
            $this->messageManager->addError(__('There is no sample file for this entity.'));

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/form');

            return $resultRedirect;
        }

        try {
            $fileContents = $this->sampleFileProvider->getFileContents($entityName);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('There is no sample file for this entity.'));
            return false;
        }

        $fileSize = $this->sampleFileProvider->getSize($entityName);
        $fileName = $entityName . '.csv';

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

    /**
     * {@inherited}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CTCD_Category::import');
    }
}
