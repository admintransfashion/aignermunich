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

namespace CTCD\Category\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;
use CTCD\Category\Api\Data\CategoryImportInterface;
use CTCD\Category\Api\Data\CategoryImportDataInterface;
use CTCD\Category\Api\ImporterInterface;
use Amasty\ShopbyCategory\Helper\Data as CategoryHelper;
use Amasty\ShopbyBase\Helper\FilterSetting as FilterSettingHelper;
use Amasty\ShopbyBase\Model\OptionSetting;

/**
 * Class Importer
 */
class Importer implements ImporterInterface
{
    /**
     * import id
     */
    protected $importId;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Catalog\Api\Data\CategoryInterfaceFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \CTCD\Category\Api\CategoryImportRepositoryInterface
     */
    protected $importRepository;

    /**
     * @var \CTCD\Category\Model\ResourceModel\CategoryImportData\CollectionFactory
     */
    protected $importDataCollection;

    /**
     * @var \CTCD\Category\Api\Data\CategoryImportDataInterfaceFactory
     */
    protected $importData;

    /**
     * @var \CTCD\Category\Api\CategoryImportDataRepositoryInterface
     */
    protected $importDataRepository;

    /**
     * @var \CTCD\Category\Api\CategoryMapRepositoryInterface
     */
    protected $mapRepository;

    /**
     * @var \CTCD\Category\Api\CategoryImportWaitRepositoryInterface
     */
    protected $waitRepository;

    /**
     * @var \CTCD\Category\Api\Data\CategoryImportWaitInterfaceFactory
     */
    protected $waitData;

    /**
     * @var \CTCD\Category\Api\Data\CategoryImportMapInterfaceFactory
     */
    protected $mapData;

    /**
     * @var \CTCD\Core\Helper\File
     */
    protected $fileHelper;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $fileIo;

    /**
     * @var \CTCD\Category\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Catalog\Api\Data\CategoryInterfaceFactory $categoryFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \CTCD\Category\Model\ResourceModel\CategoryImportData\CollectionFactory $importDataCollection
     * @param \CTCD\Category\Api\CategoryImportRepositoryInterface $importRepository
     * @param \CTCD\Category\Api\Data\CategoryImportDataInterfaceFactory $importData
     * @param \CTCD\Category\Api\CategoryImportDataRepositoryInterface $importDataRepository
     * @param \CTCD\Category\Api\CategoryMapRepositoryInterface $mapRepository
     * @param \CTCD\Category\Api\CategoryImportWaitRepositoryInterface $waitRepository
     * @param \CTCD\Category\Api\Data\CategoryImportWaitInterfaceFactory $waitData
     * @param \CTCD\Category\Api\Data\CategoryImportMapInterfaceFactory $mapData
     * @param \CTCD\Core\Helper\File $fileHelper
     * @param \CTCD\Category\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Api\Data\CategoryInterfaceFactory $categoryFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \CTCD\Category\Model\ResourceModel\CategoryImportData\CollectionFactory $importDataCollection,
        \CTCD\Category\Api\CategoryImportRepositoryInterface $importRepository,
        \CTCD\Category\Api\Data\CategoryImportDataInterfaceFactory $importData,
        \CTCD\Category\Api\CategoryImportDataRepositoryInterface $importDataRepository,
        \CTCD\Category\Api\CategoryImportMapRepositoryInterface $mapRepository,
        \CTCD\Category\Api\CategoryImportWaitRepositoryInterface $waitRepository,
        \CTCD\Category\Api\Data\CategoryImportWaitInterfaceFactory $waitData,
        \CTCD\Category\Api\Data\CategoryImportMapInterfaceFactory $mapData,
        \CTCD\Core\Helper\File $fileHelper,
        \CTCD\Category\Helper\Config $configHelper
    )
    {
        $this->filesystem = $filesystem;
        $this->curl = $curl;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->importRepository = $importRepository;
        $this->importData = $importData;
        $this->importDataRepository = $importDataRepository;
        $this->mapRepository = $mapRepository;
        $this->waitRepository = $waitRepository;
        $this->waitData = $waitData;
        $this->mapData = $mapData;
        $this->importDataCollection = $importDataCollection;
        $this->fileHelper = $fileHelper;
        $this->configHelper = $configHelper;

        $this->fileIo = $fileHelper->getFileIo();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/import_category.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
    }

    /**
     * {@inheritdoc}
     */
    public function lateImportData(\CTCD\Category\Model\ResourceModel\CategoryImportWait\Collection $data)
    {
        $this->logger->info(__FUNCTION__ . ' start===');
        foreach($data as $row){
            $value = json_decode($row->getJsonData());
            $keys = json_decode($row->getJsonKeys());

            if(!is_array($keys)) {
                $keys = (array) $keys;
            }

            try {
                $code = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::CODE]);
                $name = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::NAME]);
                $urlKey = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::URL_KEY]);
                $isActive = (int)$this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::IS_ACTIVE]);
//                $isAnchor = (int)$this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::IS_ANCHOR]);
//                $includeMenu = (int)$this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::INCLUDE_IN_MENU]);
                $description = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::DESCRIPTION]);
//                $displayMode = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::DISPLAY_MODE]);
//                $availableSortBy = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::AVAILABLE_SORT_BY]);
//                $defaultSortBy = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::DEFAULT_SORT_BY]);
                $metaTitle = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::META_TITLE]);
                $metaKeywords = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::META_DESCRIPTION]);
                $metaDescription = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::META_DESCRIPTION]);
                $image = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::IMAGE]);

                try {
                    $arr = [CategoryImportDataInterface::CATEGORY_PARENT => $keys[CategoryImportDataInterface::CATEGORY_PARENT]];
                    $parentCategory = $this->getParentCategoryId($value, $arr);

                    if ($parentCategory instanceof \Magento\Catalog\Api\Data\CategoryInterface) {
                        $parent = $parentCategory->getId();
                        $rootCategory = $parentCategory;
                    }
                } catch (NoSuchEntityException $e) {
                    $this->logger->info($e->getMessage());
                    $this->waitRepository->delete($row);
                    $this->addToWaitingParentData($value, $keys);
                    continue;
                }

                try {
                    $category = $this->mapRepository->getCategoryByCode($code);
                } catch (NoSuchEntityException $e) {
                    $category = $this->categoryFactory->create();
                }

                $category->setName($name);
                $category->setIsActive($isActive);

                if(!$urlKey || (!$urlKey && !$category->getId())) {
                    $arr = [CategoryImportDataInterface::URL_KEY => $keys[CategoryImportDataInterface::URL_KEY]];
                    $urlKey = $this->getUrlKey($value, $arr);
                }
                $category->setUrlKey($urlKey);

                $category->setData('description', $description);
                $category->setData('meta_title', $metaTitle);
                $category->setData('meta_keywords', $metaKeywords);
                $category->setData('meta_description', $metaDescription);
                $category->setParentId($parent);

                $mediaAttribute = array ('image', 'small_image', 'thumbnail');
                $image = $this->grabImage($image, $name);
                $filePath = null;

                if(is_array($image)) {
                    $filePath = 'images' . DIRECTORY_SEPARATOR . $image['file'];
                }

                if($filePath) {
                    $category->setImage($filePath, $mediaAttribute, true, false);
                }

                // add image at Path of  pub/meida/catalog/category/catagory_img.png
                $category->setStoreId(0);
                $category->setPath($rootCategory->getPath() . DIRECTORY_SEPARATOR);

                // save category
                $savedCategory = $this->categoryRepository->save($category);

                $arr[CategoryImportDataInterface::CODE] = $keys[CategoryImportDataInterface::CODE];
                $arr[CategoryImportDataInterface::MAP_OFFLINE] = $keys[CategoryImportDataInterface::MAP_OFFLINE];
                $this->saveMapping($savedCategory, $value, $arr);

                $this->waitRepository->delete($row);
            } catch (LocalizedException $e) {
                $this->logger->info('error : ' . $e->getMessage());
                continue;
            } catch (\Exception $e) {
                $this->logger->info('error : ' . $e->getMessage());
                continue;
            }

        }
        $this->logger->info(__FUNCTION__ . ' end===');
    }

    /**
     * {@inheritdoc}
     */
    public function ImportData(CategoryImportInterface $import)
    {
        $this->logger->info(__FUNCTION__ . ' start===');
        if($import->getStatus() == CategoryImportInterface::FLAG_PENDING) {
            $import->setStatus(CategoryImportInterface::FLAG_PROGRESS);
            $this->importRepository->save($import);
        }

        $rootCategory = $this->initRootCategory();

        $this->importId = $import->getId();

        $importData = $this->initImportData();

        if(!$importData) {
            $import->setStatus(CategoryImportInterface::FLAG_SUCCESS);
            $this->importRepository->save($import);
            return true;
        }

        $keys = $importData->getKeys();

        $jsonData = json_decode($importData->getJsonData());

        foreach($jsonData as $index => $value){
            try {
                $code = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::CODE]);
                $name = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::NAME]);
                $urlKey = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::URL_KEY]);
                $isActive = (int)$this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::IS_ACTIVE]);
//                $isAnchor = (int)$this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::IS_ANCHOR]);
//                $includeMenu = (int)$this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::INCLUDE_IN_MENU]);
                $description = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::DESCRIPTION]);
//                $displayMode = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::DISPLAY_MODE]);
//                $availableSortBy = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::AVAILABLE_SORT_BY]);
//                $defaultSortBy = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::DEFAULT_SORT_BY]);
                $metaTitle = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::META_TITLE]);
                $metaKeywords = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::META_KEYWORDS]);
                $metaDescription = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::META_DESCRIPTION]);
                $image = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::IMAGE]);
                $categoryParent = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::CATEGORY_PARENT]);
                $parentCategory = $rootCategory;
                $parent = $parentCategory->getId();

                try {
                    if($categoryParent != '0' ) {
                        $checkParent = $this->getParentCategoryId($value, $keys);

                        if ($checkParent instanceof \Magento\Catalog\Api\Data\CategoryInterface) {
                            $parent = $checkParent->getId();
                            $parentCategory = $checkParent;
                        }
                    }
                } catch (NoSuchEntityException $e) {
                    $this->addToWaitingParentData($value, $keys);
                    continue;
                }

                try {
                    $category = $this->mapRepository->getCategoryByCode($code);
                } catch (NoSuchEntityException $e) {
                    $category = $this->categoryFactory->create();
                }

                $category->setName($name);
                $category->setIsActive($isActive);

                if(!$urlKey || (!$urlKey && !$category->getId())) {
                    $urlKey = $this->getUrlKey($value, $keys);
                }
                $category->setUrlKey($urlKey);

                $category->setData('description', $description);
                $category->setData('meta_title', $metaTitle);
                $category->setData('meta_keywords', $metaKeywords);
                $category->setData('meta_description', $metaDescription);
                $category->setParentId($parent);

                $mediaAttribute = array ('image', 'small_image', 'thumbnail');
                $image = $this->grabImage($image, $name);
                $filePath = null;

                if(is_array($image)) {
                    // $filePath = $image['path'] . DIRECTORY_SEPARATOR . $image['file'];
                    $filePath = 'images' . DIRECTORY_SEPARATOR . $image['file'];
                }

                if($filePath) {
                    $category->setImage($filePath, $mediaAttribute, true, false);
                }

                // add image at Path of  pub/meida/catalog/category/catagory_img.png
                $category->setStoreId(0);
                $category->setPath($parentCategory->getPath() . DIRECTORY_SEPARATOR);

                // save category
                $savedCategory = $this->categoryRepository->save($category);

                $this->saveMapping($savedCategory, $value, $keys);

            } catch (LocalizedException $e) {
                $this->logger->info('error : ' . $e->getMessage());
                continue;
            } catch (\Exception $e) {
                $this->logger->info('error : ' . $e->getMessage());
                continue;
            }

            $importData->setStatus(CategoryImportDataInterface::FLAG_SUCCESS);
            $this->importDataRepository->save($importData);
        }
        $this->logger->info(__FUNCTION__ . ' end===');
    }

    /**
     * save mapping imported data
     *
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @param array $importData
     * @param array $keys
     * @return void
     */
    protected function saveMapping($category, $importData, $keys)
    {
        if ($category instanceof \Magento\Catalog\Api\Data\CategoryInterface && is_array($importData)) {
            // end update category path
            $code = $this->fileHelper->getArrayValue($importData, $keys[CategoryImportDataInterface::CODE]);
            $offlineId = $this->fileHelper->getArrayValue($importData, $keys[CategoryImportDataInterface::MAP_OFFLINE]);

            try {
                $mapp = $this->mapRepository->getByCode($code);
            } catch (NoSuchEntityException $e) {
                $mapp = $this->mapData->create();
            }

            $mapp->setCategoryId($category->getId());
            $mapp->setCategoryCode($code);

            if ($offlineId) {
                $offlineData = explode('|', $offlineId);
                $offlineData = array_values(array_filter($offlineData));
                $offlineData = json_encode($offlineData);

                $mapp->setOfflineId($offlineData);
            }

            $this->mapRepository->save($mapp);
        }
    }

    /**
     * get parent category id
     *
     * @param array $data
     * @param array $keys
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    protected function getParentCategoryId($data, $keys)
    {
        $parent = $this->fileHelper->getArrayValue($data, $keys[CategoryImportDataInterface::CATEGORY_PARENT]);
        $parentId = (string)0;

        if ($parent != $parentId) {
            try {
                $mapData = $this->mapRepository->getByCode($parent);
                return $this->categoryRepository->get($mapData->getCategoryId());
            } catch (NoSuchEntityException $e) {
                throw new NoSuchEntityException(__("Parent data with code " . $parent .  " not found"));
            }
        }

        return $parentId;
    }

    /**
     * add data to waiting for parent
     *
     * @param array $data
     * @param array $keys
     * @return void
     */
    protected function addToWaitingParentData($data, $keys)
    {
        $code = $this->fileHelper->getArrayValue($data, $keys[CategoryImportDataInterface::CODE]);

        try {
            $waitingData = $this->waitRepository->getByCode($code);
        } catch (NoSuchEntityException $e) {
            $waitingData = $this->waitData->create();
        }

        $waitingData->setJsonData(json_encode($data));
        $waitingData->setJsonKeys(json_encode($keys));
        $waitingData->setCode($code);

        $this->waitRepository->save($waitingData);
    }

    /**
     * generate url key
     *
     * @param array $data
     * @param array $keys
     * @return string
     */
    protected function getUrlKey($data, $keys)
    {
        $urlKey = $this->fileHelper->getArrayValue($data, $keys[CategoryImportDataInterface::URL_KEY]);

        if (!$urlKey) {
            $name = $this->fileHelper->getArrayValue($value, $keys[CategoryImportDataInterface::NAME]);
            $urlKey = $name;
        }

        $urlKey = strtolower($urlKey);

        $cleanUrl = trim(preg_replace('/ +/', '', preg_replace('/[^A-Za-z0-9 ]/', '', urldecode(html_entity_decode(strip_tags($urlKey))))));

        return $cleanUrl;
    }

    /**
     * get root category
     *
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    private function initRootCategory()
    {
        $store = $this->storeManager->getStore();

        $rootCategoryid = (int) $store->getRootCategoryId();
        $this->logger->info('$rootCategoryid : ' . $rootCategoryid);

        try {
            if($rootCategoryid == 0) {
                $rootCategoryid = (int)$this->configHelper->getRootCategoryId();
            }

            $category = $this->categoryRepository->get($rootCategoryid);
            $this->logger->info('after get $rootCategoryid : ' . $category->getId());
            return $category;
        } catch (NoSuchEntityException $e) {
            $this->logger->info('catch error -> $rootCategoryid : ' . $rootCategoryid);
            $this->logger->info($e->getMessage());
        }

        return false;
    }

    /**
     * init Category import
     *
     * @return CTCD\Category\Api\Data\CategoryImportDataInterface
     */
    protected function initImportData()
    {
        $data = $this->importDataCollection->create();
        $data->addFieldToFilter('import_id', $this->importId);
        $data->addFieldToFilter('status', CategoryImportDataInterface::FLAG_PENDING);
        $data->setOrder('sequence', 'ASC');
        $data->setPageSize(1);
        $import = $data->getFirstItem();

        if ($import->getId()) {
            $import->setStatus(CategoryImportDataInterface::FLAG_PROGRESS);
            $this->importDataRepository->save($import);
            return $import;
        }

        return null;
    }

    /**
     * processing image
     * @param array $image
     * @param string $path
     * @return void
     */
    protected function processImage(array $image, string $path = null)
    {
        $amastyPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($path);
        if (!is_dir($amastyPath)) {
            $this->fileIo->mkdir($amastyPath, 0775);
        }

        $move = $this->fileIo->mv($image['path'] . DIRECTORY_SEPARATOR . $image['file'], $amastyPath);
    }

    /**
     * grab image external
     *
     * @param string $imageUrl
     * @param string $categoryName
     * @return array
     */
    protected function grabImage(string $imageUrl = null, $categoryName = null)
    {
        if(!$imageUrl) {
            return false;
        }

        $path = CategoryImportDataInterface::CATEGORY_IMAGE_PATH;

        $rawPath = pathinfo($imageUrl);

        if (!isset($rawPath['extension'])) {
            $this->logger->info("Given image URL is not valid." . $imageUrl);
            return false;
        }

        $filename = $rawPath['basename'];
        $dispersion = $this->fileHelper->getDispersionPath($filename);

        $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($path . DIRECTORY_SEPARATOR . $categoryName . $dispersion);

        if (!is_dir($path)) {
            $this->fileIo->mkdir($path, 0775);
        }

        $filePath = $path . DIRECTORY_SEPARATOR . $filename;

        if(file_exists($filePath)){
            unlink($filePath);
        }

        try {
            /** create folder if it is not exists */
            $this->fileIo->checkAndCreateFolder($path);

            $result = $this->fileIo->read($imageUrl, $filePath);
        } catch (\Exception $e) {
            $this->logger->info('catch error -> $rootCategoryid : ' . $rootCategoryid);
            return false;
        }

        return ['path' => $path, 'file' => $categoryName . $dispersion . DIRECTORY_SEPARATOR . $filename];
    }
}
