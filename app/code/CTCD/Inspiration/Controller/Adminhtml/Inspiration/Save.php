<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Controller\Adminhtml\Inspiration;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use CTCD\Inspiration\Api\InspirationRepositoryInterface;
use CTCD\Inspiration\Api\Data\InspirationInterfaceFactory;
use CTCD\Inspiration\Api\Data\InspirationInterface;

class Save extends \CTCD\Inspiration\Controller\Adminhtml\Inspiration
{
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var InspirationInterfaceFactory
     */
    protected $inspirationFactory;

    /**
     * @var InspirationRepositoryInterface
     */
    protected $inspirationRepository;

    /**
     * @var \CTCD\Core\Helper\Url
     */
    protected $coreUrlHelper;

    /**
     * @var \CTCD\Inspiration\Helper\Data
     */
    protected $inspirationHelper;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param InspirationRepositoryInterface $inspirationRepository
     * @param InspirationInterfaceFactory $inspirationFactory
     * @param \CTCD\Core\Helper\Url $coreUrlHelper
     * @param \CTCD\Inspiration\Helper\Data $inspirationHelper
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        DataObjectProcessor $dataObjectProcessor,
        InspirationRepositoryInterface $inspirationRepository,
        InspirationInterfaceFactory $inspirationFactory,
        \CTCD\Core\Helper\Url $coreUrlHelper,
        \CTCD\Inspiration\Helper\Data $inspirationHelper
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->inspirationFactory = $inspirationFactory;
        $this->inspirationRepository = $inspirationRepository;
        $this->coreUrlHelper = $coreUrlHelper;
        $this->inspirationHelper = $inspirationHelper;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $resultForwardFactory, $inspirationRepository);
    }

    public function execute()
    {
        $inspiration = null;

        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        $inspirationId = array_key_exists(InspirationInterface::ENTITY_ID, $data) ? $data[InspirationInterface::ENTITY_ID] : null;

        if (array_key_exists(InspirationInterface::URL_KEY, $data)) {
            $urlKey = $this->coreUrlHelper->urlSlug($data[InspirationInterface::URL_KEY]);
            if($this->isUrlKeyUsed($urlKey, $inspirationId)){
                $this->messageManager->addErrorMessage(__('The URL key "%1" has been used by other inspiration. Please use another key.', $urlKey));
                if ($inspiration != null) {
                    $this->storeInspirationDataToSession(
                        $this->dataObjectProcessor->buildOutputDataArray(
                            $inspiration,
                            InspirationInterface::class
                        )
                    );
                }
                if ($inspirationId) {
                    $resultRedirect->setPath('ctcdinspiration/inspiration/edit', ['id' => $inspirationId]);
                } else {
                    $resultRedirect->setPath('ctcdinspiration/inspiration');
                }
                return $resultRedirect;
            }

            $data[InspirationInterface::URL_KEY] = $urlKey;
        }

        try {
            if(!$inspirationId && array_key_exists(InspirationInterface::ENTITY_ID, $data)){
                unset($data[InspirationInterface::ENTITY_ID]);
            }

            $inspirationTitle = $data[InspirationInterface::TITLE];
            $successMessage = $inspirationId ? __('Inspiration "'.$inspirationTitle.'" has been updated successfully') : __('New inspiration "'.$inspirationTitle.'" has been added successfully');

            $inspiration = $this->inspirationFactory->create();
            $inspiration->setData($data);
            $this->inspirationRepository->save($inspiration);
            $this->messageManager->addSuccessMessage($successMessage);

            $resultRedirect->setPath('ctcdinspiration/inspiration');

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem when saving the inspiration data'));
            if ($inspiration != null) {
                $this->storeInspirationDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $inspiration,
                        InspirationInterface::class
                    )
                );
            }

            if ($inspirationId) {
                $resultRedirect->setPath('ctcdinspiration/inspiration/edit', ['id' => $inspirationId]);
            } else {
                $resultRedirect->setPath('ctcdinspiration/inspiration');
            }
        }

        return $resultRedirect;
    }

    /**
     * Check whether the url key already used or not by other inspiration
     *
     * @param string $urlKey
     * @param int|null $inspirationId
     * @return bool
     */
    protected function isUrlKeyUsed($urlKey, $inspirationId = null)
    {
        $used = false;

        if($urlKey !== null){
            try {
                $inspiration = $this->inspirationRepository->get($urlKey);
            } catch (\Exception $e) {
                $inspiration = null;
            }

            if($inspiration){
                if($inspirationId === null || ($inspirationId !== null && $inspiration->getId() != $inspirationId)){
                    $used = true;
                }
            }
        }

        return $used;
    }

    /**
     * @param $sessionData
     */
    protected function storeInspirationDataToSession($sessionData)
    {
        $this->_getSession()->setInspirationInspirationData($sessionData);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CTCD_Inspiration::inspiration_update');
    }
}
