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

namespace Trans\Reservation\Controller\Adminhtml\Config;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\NoSuchEntityException;
use Trans\Reservation\Model\Config\FileUploader;
use Trans\Reservation\Api\Data\ReservationConfigInterface;
use Trans\Reservation\Model\Config\Source\ImportBehavior;
use Trans\Reservation\Model\Config\Source\ErrorBehavior;

/**
 * Class Importpost
 */
class Importpost extends \Trans\Reservation\Controller\Adminhtml\Config
{
    /**
     * Const for csv column name
     */
    const COLUMN_NAME_ARRAY = ["store", "sku", "value", "config","flag"];

    /**
     * Const for csv array index sku
     */
    const STORE = 0;
    const SKU = 1;
    const VALUE = 2;
    const CONFIG = 3;
    const FLAG = 4;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * @var \Trans\Reservation\Api\ReservationConfigRepositoryInterface
     */
    protected $configRepository;

    /**
     * @var \Trans\Reservation\Api\Data\ReservationConfigInterfaceFactory
     */
    protected $configInterfaceFactory;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var json
     */
    protected $json;

    /**
     * @var logger
     */
    protected $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory $collectionFactory
     * @param \Trans\Reservation\Model\ReservationConfigFactory $configFactory
     * @param \Trans\Reservation\Api\ReservationConfigRepositoryInterface $configRepository
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Trans\Reservation\Api\Data\ReservationConfigInterfaceFactory $configInterfaceFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory $collectionFactory,
        \Trans\Reservation\Model\ReservationConfigFactory $configFactory,
        \Trans\Reservation\Api\ReservationConfigRepositoryInterface $configRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Reservation $reservationHelper,
        ManagerInterface $messageManager,
        Filesystem $filesystem,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Trans\Reservation\Api\Data\ReservationConfigInterfaceFactory $configInterfaceFactory
    )
    {
        $this->messageManager = $messageManager;
        $this->filesystem = $filesystem;
        $this->csv = $csv;
        $this->file = $file;
        $this->configInterfaceFactory = $configInterfaceFactory;
        $this->configRepository = $configRepository;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

        parent::__construct(
            $context,
            $categoryRepository,
            $collectionFactory,
            $configFactory,
            $configRepository,
            $dataHelper,
            $reservationHelper
        );
    }

    /**
     * Import bulk
     */
    public function execute()
    {
        $this->logger->info("       ");
        $this->logger->info("---------- Start Import Config -------------");

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $err = 0;
        $saves = 0;
        $post = $this->getRequest()->getPost();
        $fileName = $post['file'][0]['name'];
        $filePath = FileUploader::BASE_TMP_PATH . '/' . $fileName;
        $path = $this->mediaDirectory->getAbsolutePath($filePath);
        $errorBehavior = $post['ErrorBehavior'];

        if($fileName && $this->file->isExists($path)) {
            try {
                $csvData = $this->csv->getData($path);

                foreach ($csvData as $row => $data) {
                    if($row == 0) {
                        $validateColumnName = $this->validateColumnName($data);

                        if($validateColumnName['result'] == false) {
                            if(is_array($validateColumnName['column'])) {
                                $string = implode(', ', $validateColumnName['column']);
                                $this->messageManager->addErrorMessage(__("Column(s) %1 not found.", $string));

                                $this->logger->info(__("Column(s) %1 not found.", $string));
                                $this->logger->info("---------- End Import Config -------------");
                                $this->logger->info("    ");

                            } else {
                                $this->messageManager->addErrorMessage(__("Wrong column order."));
                                $this->logger->info(__("Wrong column order."));
                            }

                            /** delete file */
                            $this->file->deleteFile($path);
                            return $resultRedirect->setPath('*/*/import');
                        }
                    }

                    if ($row > 0){
                        $numb = $row+1;

                        if(empty($data[self::VALUE])) {
                            // validate value cannot empty
                            $this->messageManager->addErrorMessage(__('Row %1, value cannot empty.', $numb));
                            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                            if($errorBehavior == ErrorBehavior::STOP) {
                                /** delete file */
                                $this->file->deleteFile($path);
                                return $resultRedirect;
                            } else {
                                $err++;
                                continue;
                            }
                        }

                        if(empty($data[self::CONFIG])) {
                            // validate config cannot empty
                            $this->messageManager->addErrorMessage(__('Row %1, config cannot empty.', $numb));
                            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                            if($errorBehavior == ErrorBehavior::STOP) {
                                /** delete file */
                                $this->file->deleteFile($path);
                                return $resultRedirect;
                            } else {
                                $err++;
                                continue;
                            }
                        }

                        $validateValue = $this->validateValue($data[self::VALUE], $data[self::CONFIG], true);
                        if($validateValue['result'] == false) {
                            $this->messageManager->addErrorMessage($validateValue['message']);
                            $this->file->deleteFile($path);
                            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                            if($errorBehavior == ErrorBehavior::STOP) {
                                /** delete file */
                                $this->file->deleteFile($path);
                                return $resultRedirect;
                            } else {
                                $err++;
                                continue;
                            }
                        }

                        try {
                            $config = $this->configRepository->getByValue($data[self::VALUE], $data[self::CONFIG]);

                            if($post['behavior'] == ImportBehavior::DELETE) {
                                $this->configRepository->delete($config);
                                $err++;
                                continue;
                            }
                        } catch (NoSuchEntityException $e) {
                            $config = $this->configInterfaceFactory->create();
                        }

                        $title = $config->getTitle();
                        if(!$title) {
                            $title = $this->reservationHelper->generateTitle($data[self::CONFIG], $data[self::VALUE]);
                        }

                        $filter = ReservationConfigInterface::FILTER_PRODUCT;
                        if($config->getFilter()) {
                            if($config->getFilter() == ReservationConfigInterface::FILTER_CATEGORY || $config->getCategoryId()) {
                                $filter = ReservationConfigInterface::FILTER_ALL;
                            }
                        }


                        if(empty($data[self::SKU])) {
                            // validate product sku cannot empty
                            $this->messageManager->addErrorMessage(__('Row %1, product SKU cannot empty.', $numb));
                            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                            if($errorBehavior == ErrorBehavior::STOP) {
                                /** delete file */
                                $this->file->deleteFile($path);
                                return $resultRedirect;
                            } else {
                                $err++;
                                continue;
                            }
                        }

                        if(empty($data[self::STORE])) {
                            // validate store cannot empty
                            $this->messageManager->addErrorMessage(__('Row %1, store cannot empty.', $numb));
                            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                            if($errorBehavior == ErrorBehavior::STOP) {
                                /** delete file */
                                $this->file->deleteFile($path);
                                return $resultRedirect;
                            } else {
                                $err++;
                                continue;
                            }
                        }

                        if(!empty($data[self::SKU])) {
                            $skuArray = [];
                            $productSku = $config->getProductSku();
                            $this->logger->info('$productSku = ' . $productSku);
                            if($productSku != '') {
                                $skuArray = $this->json->unserialize($productSku);
                            }
                            $skuArray[] = $data[self::SKU];

                            $data[self::SKU] = $this->json->serialize($skuArray);
                        }

                        if(!empty($data[self::STORE])) {
                            $storeArray = [];
                            $store = $config->getStore();
                            $this->logger->info('$store = ' . $store);
                            if($store != '') {
                                $storeArray = $this->json->unserialize($store);
                            }
                            $storeArray[] = $data[self::STORE];

                            $data[self::STORE] = $this->json->serialize($storeArray);
                        }

                        $config->setTitle($title);
                        $config->setValue($data[self::VALUE]);
                        $config->setConfig($data[self::CONFIG]);
                        $config->setFlag($data[self::FLAG]);
                        $config->setStore($data[self::STORE]);
                        $config->setFilter($filter);

                        $config->setProductSku($data[self::SKU]);

                        $this->configRepository->save($config);
                        $saves++;
                    }
                }

                if($saves > 0) {
                    $this->messageManager->addSuccessMessage(__('%1 data(s) Imported.', $saves));
                    $this->logger->info("success");
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->info("Error import config. Message = " . $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while import config.'));
                $this->logger->info("Error import config. Message = " . $e->getMessage());
            }

            /** delete file */
            $this->file->deleteFile($path);
        } else {
            $this->messageManager->addErrorMessage("No file have been uploaded.");
            $this->logger->info("No file have been uploaded.");
        }

        $this->logger->info("---------- End Import Config -------------");
        $this->logger->info("    ");

        return $resultRedirect->setPath('*/*/import');
    }

    /**
     * prepare category ids data
     *
     * @param string $param
     * @return json|bool
     */
    protected function prepareCategoryIds(string $param)
    {
        if($param) {
            $categoryIds = explode(',', $param);

            return $this->json->serialize(array_filter($categoryIds));
        }

        return false;
    }

    /**
     * prepare product skus data
     *
     * @param string $param
     * @return json|bool
     */
    protected function prepareSkus(string $param)
    {
        if($param) {
            $skus = explode(',', $param);

            return $this->json->serialize(array_unique($skus));
        }

        return false;
    }

    /**
     * validate csv column name
     *
     * @param array $date
     * @return array
     */
    protected function validateColumnName(array $data)
    {
        $columnName = self::COLUMN_NAME_ARRAY;
        $csvColumnName = $data;

        $diff = array_diff($columnName, $csvColumnName);
        if(empty($diff)) {
            foreach ($csvColumnName as $key => $value) {
                if($value != $columnName[$key]) {
                    return ['result' => false, 'column' => $value];
                }
            }
            return ['result' => true, 'column' => null];
        }

        return ['result' => false, 'column' => $diff];
    }
}
