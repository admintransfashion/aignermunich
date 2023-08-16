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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use CTCD\Category\Api\Data\CategoryImportInterface;

/**
 * Class Import
 */
class Import extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'CTCD_Category::category_import';

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $fileIo;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;

    /**
     * @var \CTCD\Category\Model\ResourceModel\CategoryImport\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \CTCD\Category\Api\Data\CategoryImportInterfaceFactory
     */
    protected $categoryImportFactory;

    /**
     * @var \CTCD\Category\Api\CategoryImportRepositoryInterface
     */
    protected $categoryImportRepository;

    /**
     * @param Context $context
     * @param \Magento\Framework\Filesystem\Io\File $fileIo
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Catalog\Api\PorductRepositoryInterface $productRepository
     * @param \CTCD\Category\Model\ResourceModel\CategoryImport\CollectionFactory $collectionFactory
     * @param \CTCD\Category\Api\Data\CategoryImportInterfaceFactory $categoryImportFactory
     * @param \CTCD\Category\Api\CategoryImportRepositoryInterface $categoryImportRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem\Io\File $fileIo,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Csv $csv,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \CTCD\Category\Model\ResourceModel\CategoryImport\CollectionFactory $collectionFactory,
        \CTCD\Category\Api\Data\CategoryImportInterfaceFactory $categoryImportFactory,
        \CTCD\Category\Api\CategoryImportRepositoryInterface $categoryImportRepository
    )
    {
        $this->fileIo = $fileIo;
        $this->filesystem = $filesystem;
        $this->csv = $csv;
        $this->productRepository = $productRepository;
        $this->collectionFactory = $collectionFactory;
        $this->categoryImportFactory = $categoryImportFactory;
        $this->categoryImportRepository = $categoryImportRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $file = $data[CategoryImportInterface::FILE][0];

        if($data) {
            try {
                $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . CategoryImportInterface::FILE_PATH;

                if (!is_dir($path)) {
                    $this->fileIo->mkdir($path, 0775);
                }

                $csvData = $this->csv->getData($path . $file['file']);
                $verify = $this->verifyColumn($csvData);

                $model = $this->initCategoryImport();
                $model->setFile($file['file']);
                $model->setStatus(CategoryImportInterface::FLAG_PENDING);

                $categoryImport = $this->categoryImportRepository->save($model);

                $this->messageManager->addSuccessMessage('Import data processing.');

                $this->_eventManager->dispatch('submit_category_import_success', ['categoryImport' => $categoryImport]);

            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } else {
            $this->messageManager->addErrorMessage('Failed, data input empty.');
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * init CategoryImport
     *
     * @return \CTCD\Category\Api\Data\CategoryImportInterface
     */
    protected function initCategoryImport()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                return $this->categoryImportRepository->getById($id);
            } catch (NoSuchEntityException $e) {
            }
        }

        return $this->categoryImportFactory->create();
    }

    /**
     * Verify csv column
     *
     * @param array $data
     * @return bool
     */
    protected function verifyColumn(array $data)
    {
        $label = ["value","description","small_image","large_image"];

        $diff = array_diff($data[0], $label);

        if(!$diff) {
            return true;
        }
    }

    /**
     * {@inherited}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CTCD_Category::import');
    }
}
