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
use Magento\Framework\Controller\ResultFactory;
use CTCD\Category\Api\Data\CategoryImportInterface;

/**
 * Class Uploader
 */
class Uploader extends \Magento\Backend\App\Action
{
    /**
     * uploader
     * @var \Magento\MediaStorage\Model\File\Uploader
     */
    protected $uploader;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $fileIo;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $datetime;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * Upload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\MediaStorage\Model\File\Uploader $uploader
     * @param \Magento\Catalog\Model\ImageUploader $imageUploader
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\File $fileIo
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploader,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $fileIo,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Backend\Model\Auth\Session $authSession
    ){
        parent::__construct($context);
        $this->uploader = $uploader;
        $this->filesystem = $filesystem;
        $this->fileIo = $fileIo;
        $this->storeManager = $storeManager;
        $this->datetime = $datetime;
        $this->authSession = $authSession;
    }

    /**
     * Upload file controller action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $uploadId = $this->getRequest()->getParam('param_name', 'file');

        try {
            $uploader = $this->uploader->create(['fileId' => $uploadId]);
            $uploader->setAllowedExtensions(['csv']);
            $uploader->setAllowCreateFolders(false);
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);

            // get media directory
            $basePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . CategoryImportInterface::FILE_PATH;
            $newFileName = CategoryImportInterface::PREFIX_IMPORT_FILE.'_'.$this->datetime->gmtDate('YmdHis').'_'.$this->authSession->getUser()->getId().'.csv';

            // save the file to media directory
            $result = $uploader->save($basePath, $newFileName);

            // Upload file folder wise
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];

            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $result['url'] = $mediaUrl . CategoryImportInterface::FILE_PATH . $result['file'];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * {@inherited}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CTCD_Category::import');
    }
}
