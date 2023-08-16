<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Core\Helper;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;

/**
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class Logger extends \Magento\Framework\App\Helper\AbstractHelper
{

    const BASE_LOG_PATH = 'var/log/ctcd';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $writeDirectory;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList

    ) {
        $this->timezone = $timezone;
        $this->fileSystem = $fileSystem;
        $this->directoryList = $directoryList;
        $this->writeDirectory = $fileSystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
        parent::__construct($context);
    }

    /**
     * @param string $field
     * @param string|null $scope
     * @param int|null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $scope = null, $storeId = null)
    {
        $scope = !$scope ? ScopeInterface::SCOPE_WEBSITE : $scope;
        return $this->scopeConfig->getValue(
            $field,
            $scope,
            $storeId
        );
    }

    /**
     * Generate log id for troubleshoot purpose
     *
     * @return string
     */
    public function generateLogId()
    {
        return '#'. date('YmdHis') . rand(1111111, 9999999);
    }

    /**
     * Write log with title to file
     *
     * @return bool
     */
    public function writeLogWithTitle($prefixFilename, $title, $message)
    {
        if(is_array($message) || is_object($message)) {
            if($title) {
                $this->writeLogByDate($prefixFilename, $title);
            }
            $this->writeLogByDate($prefixFilename, $message);
        }
        else{
            $this->writeLogByDate($prefixFilename, $title.$message);
        }

        return true;
    }

    /**
     * @param null $message
     * @return bool
     */
    public function writeLogByDate($prefixFilename = '', $message = null)
    {
        $flag = true;
        $isFileLoggerFeatureEnabled = (bool) $this->getConfigValue('ctcdcore/file_logger/active');
        if($isFileLoggerFeatureEnabled && $prefixFilename && $message) {
            $prefixFilename = preg_replace('/\s+/', '_', strtolower(trim($prefixFilename)));
            $folderYear = $this->timezone->date()->format('Y');
            $folderMonth = $this->timezone->date()->format('m');
            $folderDate = $this->timezone->date()->format('d');
            $filename = $prefixFilename . '-'.$this->timezone->date()->format('Ymd').'.log';
            $fullname = $folderYear . '/' . $folderMonth . '/' . $folderDate . '/' . $filename;
            $flag = $this->write($message, $fullname);
        }
        return $flag;
    }

    /**
     * Write custom log
     *
     * @param string $message
     * @param string $logFile
     * @return boolean
     */
    public function write($message = null, $logFile = 'customlogger.log')
    {
        if ($message && is_string($logFile) && $logFile !== '') {
            try {
                // Trim whitespaces
                $logFile = trim($logFile);

                // Replace backslash with slash
                $logFile = str_replace('\\', '/', $logFile);

                // Replace multiple slashes with one slash
                $logFile = preg_replace('~/+~', '/', $logFile);

                // Trim slashes
                $logFile = trim($logFile, '/');

                if(strpos($logFile, '/') !== false){
                    $paths = array_map('trim', explode('/', $logFile));
                    $fileName = end($paths);
                    array_pop($paths);
                    $filePath = implode('/', $paths);
                    $this->createDirectory($filePath);
                    $logFile = $filePath . '/' . $fileName;
                }

                $fullpath = $this->directoryList->getRoot() . '/' . self::BASE_LOG_PATH . '/' . trim($logFile);

                $writer = new \Zend\Log\Writer\Stream($fullpath);
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);

                if(is_array($message) || is_object($message)){
                    $logger->info(print_r($message, 1));
                }
                else{
                    $logger->info($message);
                }

                return true;

            } catch (\Exception $e) {
            }
        }

        return false;
    }

    /**
     * Create directory/folder
     *
     * @param string $path
     * @return bool
     */
    protected function createDirectory($path)
    {
        if($path) {
            $logPath = $this->directoryList->getRoot() . '/' . self::BASE_LOG_PATH . '/' . trim(trim($path));
            if (! $this->writeDirectory->isDirectory($path)) {
                try {
                    $directory = $this->writeDirectory->create($logPath);
                } catch (FileSystemException $e) {
                }
            }

            return $directory;
        }
    }

}
