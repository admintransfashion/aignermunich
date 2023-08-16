<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 */

namespace CTCD\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class File extends AbstractHelper
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var Magento\Framework\Filesystem\Io\File
     */
    protected $fileIo;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param DirectoryList $directoryList
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\Filesystem\Io\File $fileIo
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        DirectoryList $directoryList,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Filesystem\Io\File $fileIo,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->directoryList = $directoryList;
        $this->mediaConfig = $mediaConfig;
        $this->fileSystem = $fileSystem;
        $this->fileIo = $fileIo;
        $this->logger = $logger;
        $this->mediaDirectory = $fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * get file io
     *
     * @return Magento\Framework\Filesystem\Io\File
     */
    public function getFileIo()
    {
        return $this->fileIo;
    }

	/**
     * Add directory separator
     *
     * @param string $dir
     * @return string
     */
    public function addDirSeparator($dir)
    {
        if (substr($dir, -1) != '/') {
            $dir .= '/';
        }
        return $dir;
    }

    /**
     * Get dispersion path
     *
     * @param string $fileName
     * @return string
     */
    public function getDispersionPath($fileName)
    {
        $char = 0;
        $dispersionPath = '';
        while ($char < 2 && $char < strlen($fileName)) {
            if (empty($dispersionPath)) {
                $dispersionPath = '/' . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            } else {
                $dispersionPath = $this->addDirSeparator(
                    $dispersionPath
                ) . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            }
            $char++;
        }

        return strtolower($dispersionPath);
    }

    /**
     * generate image path
     *
     * @param string $filename
     * @return string
     */
    public function generateFilePath(string $filename, string $path = null)
    {
        $dispersionPath = $this->getDispersionPath($filename);
        // get media directory
        $basePath = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . $path . $dispersionPath;

        return $basePath;
    }

    /**
     * get array value
     *
     * @param array $data
     * @param string $key
     * @return string|null
     */
    public function getArrayValue($data, $key)
    {
        $value = null;

        if (isset($data[$key])) {
            $value = $data[$key];
        }

        return $value;
    }

    /**
     * get image mime
     *
     * @param string $imagePath
     * @return string
     */
    public function getImageMime($imagePath)
    {
        $kind = '';
        try {
            $imageInfo = getimagesize($imagePath);
            $mime = explode('/', $imageInfo['mime']);
            $kind = strtoupper($mime[1]);
        } catch (\Exception $e) {

        }
        return $kind == 'JPEG' ? 'JPG' : $kind;
    }

    /**
     * Get Media Path
     *
     * @return string
     */
    public function getMediaPath() {
        return $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();;
    }

    /**
     * Media directory name for the temporary file storage
     * pub/media/tmp
     *
     * @return string
     */
    protected function getMediaDirTmpDir() {

        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
    }

    /**
     * Get filename which is not duplicated with other files in media temporary and media directories
     *
     * @param string $fileName
     * @param string $dispersionPath
     * @return string
     * @since 101.0.0
     */
    protected function getNotDuplicatedFilename($fileName, $dispersionPath)
    {
        $fileMediaName = $dispersionPath . '/' . \Magento\MediaStorage\Model\File\Uploader::getNewFileName($this->mediaConfig->getMediaPath($fileName));
        $fileTmpMediaName = $dispersionPath . '/' . \Magento\MediaStorage\Model\File\Uploader::getNewFileName($this->mediaConfig->getTmpMediaPath($fileName));

        if ($fileMediaName != $fileTmpMediaName) {
            if ($fileMediaName != $fileName) {
                return $this->getNotDuplicatedFilename(
                    $fileMediaName,
                    $dispersionPath
                );
            } elseif ($fileTmpMediaName != $fileName) {
                return $this->getNotDuplicatedFilename(
                    $fileTmpMediaName,
                    $dispersionPath
                );
            }
        }

        return $fileMediaName;
    }

    /**
     * get image file
     *
     * @param string $imageUrl
     * @param string $destinationPath
     * @return array
     */
    public function getImageFile($imageUrl, $destinationPath = '', $newName = '')
    {
        $tmpDir = $this->getMediaDirTmpDir();
        $tmp = [];
        try {
            $this->fileIo->checkAndCreateFolder($tmpDir);
            $filename = baseName($imageUrl);
            $imgName = $tmpDir . baseName($imageUrl);
            $fileName = \Magento\MediaStorage\Model\File\Uploader::getCorrectFileName($filename);
            $dispersionPath = \Magento\MediaStorage\Model\File\Uploader::getDispersionPath($filename);
            $fileName = $dispersionPath . '/' . $fileName;
            $fileName = $this->getNotDuplicatedFilename($fileName, $dispersionPath);
            $destinationFile = $this->mediaConfig->getMediaPath($fileName);
            if(!empty($newName)) {
                $fileName = $newName;
            }
            if($destinationPath) {
                $destinationFile = $destinationPath . '/' . $fileName;
            }
            $this->moveFile($imgName, $destinationFile);

            $tmp['tmp'] = $imgName;
            $tmp['filename'] = $filename;
            $tmp['dispertion_filename'] = $fileName;
        } catch (\Exception $exception) {
        }

        return $tmp;
    }

    /**
     * move file
     *
     * @param string $sourceFile
     * @param string $destinationFile
     * @return void
     */
    public function moveFile($sourceFile, $destinationFile)
    {
        $this->mediaDirectory->copyFile(
            $sourceFile,
            $destinationFile
        );
    }

    /**
     * Generate UUID
     *
     * @return string
     */
    public function generateUUID()
    {
        $hash = sha1(uniqid(date('YmdHis'), true));
        return sprintf('%08s-%04s-%04x-%04x-%12s',

            // 32 bits for "time_low"
            substr($hash, 0, 8),

            // 16 bits for "time_mid"
            substr($hash, 8, 4),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 5
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

            // 48 bits for "node"
            substr($hash, 20, 12)
        );
    }
}
