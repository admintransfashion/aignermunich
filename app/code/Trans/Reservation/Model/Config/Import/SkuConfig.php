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

namespace Trans\Reservation\Model\Config\Import;

use Magento\Framework\Filesystem;
use Trans\Reservation\Model\Config\FileUploader;

/**
 * Class SkuConfig
 */
class SkuConfig
{
    /**
     * Const for csv column name
     */
    // const COLUMN_NAME_ARRAY = ["title","value","config","filter","flag","category_ids","product_skus"];
    const COLUMN_NAME_ARRAY = ["sku"];

    /**
     * Const for csv array index
     */
    const SKU = 0;
    const TITLE = 0;
    const VALUE = 1;
    const CONFIG = 2;
    const FILTER = 3;
    const FLAG = 4;
    const CATEGORY_IDS = 5;
    const PRODUCT_SKUS = 6;

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
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationHelper;

    /**
     * @var \Trans\Reservation\Model\ReservationConfigFactory
     */
    protected $configFactory;

    /**
     * @var json
     */
    protected $json;

    /**
     * @var logger
     */
    protected $logger;

    /**
     * @var data post
     */
    protected $dataPost;

    /**
     * @var data config
     */
    protected $dataConfig;

    /**
     * @param \Trans\Reservation\Model\ReservationConfigFactory $configFactory
     * @param \Trans\Reservation\Api\ReservationConfigRepositoryInterface $configRepository
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Helper\Reservation $reservationHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Trans\Reservation\Api\Data\ReservationConfigInterfaceFactory $configInterfaceFactory
     */
    public function __construct(
        \Trans\Reservation\Model\ReservationConfigFactory $configFactory,
        \Trans\Reservation\Api\ReservationConfigRepositoryInterface $configRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Reservation $reservationHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Filesystem $filesystem,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Trans\Reservation\Api\Data\ReservationConfigInterfaceFactory $configInterfaceFactory
    )
    {
        $this->configFactory = $configFactory;
        $this->configRepository = $configRepository;
        $this->dataHelper = $dataHelper;
        $this->reservationHelper = $reservationHelper;
        $this->messageManager = $messageManager;
        $this->filesystem = $filesystem;
        $this->csv = $csv;
        $this->file = $file;
        $this->configInterfaceFactory = $configInterfaceFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

        $this->json = $dataHelper->getJson();
        $this->logger = $dataHelper->getLogger();
    }

    /**
     * set data post
     * @param array $data
     */
    public function setDataPost($data)
    {
        $this->dataPost = $data;
    }

    /**
     * get data post
     * @return array
     */
    public function getDataPost()
    {
        return $this->dataPost;
    }

    /**
     * set data config
     *
     * @param \Trans\Reservation\Api\Data\ReservationConfigInterface
     */
    public function setDataConfig($data)
    {
        $this->dataConfig = $data;
    }

    /**
     * get data config
     *
     * @param \Trans\Reservation\Api\Data\ReservationConfigInterface
     */
    public function getDataConfig()
    {
        return $this->dataConfig;
    }

    /**
     * Import bulk
     */
    public function execute()
    {
        $this->logger->info("       ");
        $this->logger->info("---------- Start Import Config -------------");

        $err = 0;
        $saves = 0;
        $post = $this->getDataPost();
        $fileName = $post['file'][0]['name'];
        $filePath = FileUploader::BASE_TMP_PATH . '/' . $fileName;
        $path = $this->mediaDirectory->getAbsolutePath($filePath);
        $model = $this->getDataConfig();

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
                            return false;
                        }
                    }

                    if ($row > 0){
                        $numb = $row+1;

                        if(empty($data[self::SKU])) {
                            // validate config cannot empty
                            $this->messageManager->addErrorMessage(__('Row %1, sku cannot empty.', $numb));
                            $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                            $err++;
                            continue;
                        }

                        $productSku = [];
                        try {
                            $config = $this->configRepository->getById($model->getId());

                            if($model->getProductSku()) {
                                $productSku = $this->json->unserialize($model->getProductSku());
                            }
                        } catch (NoSuchEntityException $e) {
                            $config = $this->configInterfaceFactory->create();
                        }

                        $productSku[] = $data[self::SKU];
                        $productSku = $this->json->serialize(array_unique($productSku));

                        $config->setProductSku($productSku);

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
                throw new \Exception($e->getMessage());
                return false;
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while import config.'));
                $this->logger->info("Error import config. Message = " . $e->getMessage());
                throw new \Exception($e->getMessage());
                return false;
            }

            /** delete file */
            $this->file->deleteFile($path);
        } else {
            $this->messageManager->addErrorMessage("No file have been uploaded.");
            $this->logger->info("No file have been uploaded.");
            throw new \Exception("No file have been uploaded.");
            return false;
        }

        $this->logger->info("---------- End Import Config -------------");
        $this->logger->info("    ");

        return true;
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

            return $this->json->serialize(array_filter($skus));
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
