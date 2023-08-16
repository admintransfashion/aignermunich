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

namespace Trans\Reservation\Controller\Adminhtml\Date;

use Magento\Framework\Exception\LocalizedException;
use Trans\Reservation\Api\Data\ReservationConfigInterface;

/**
 * Class Save
 */
class Save extends \Trans\Reservation\Controller\Adminhtml\Config
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Trans_Reservation::reservation';

    /**
     * @var \Trans\Reservation\Model\Config\Import\SkuConfigFactory
     */
    protected $skuBulk;

    /**
     * @param Context $context
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory $collectionFactory
     * @param \Trans\Reservation\Model\ReservationConfigFactory $configFactory
     * @param \Trans\Reservation\Api\ReservationConfigRepositoryInterface $configRepository
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Helper\Reservation $reservationHelper
     * @param \Trans\Reservation\Model\Config\Import\SkuConfigFactory $skuBulk
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory $collectionFactory,
        \Trans\Reservation\Model\ReservationConfigFactory $configFactory,
        \Trans\Reservation\Api\ReservationConfigRepositoryInterface $configRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Reservation $reservationHelper,
        \Trans\Reservation\Model\Config\Import\SkuConfigFactory $skuBulk
    )
    {
        $this->skuBulk = $skuBulk;

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
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if($data) {
            $data[ReservationConfigInterface::CONFIG] = ReservationConfigInterface::CONFIG_DATE;

            if(empty($data[ReservationConfigInterface::TITLE])) {
                $data[ReservationConfigInterface::TITLE] = $this->generateTitle(ReservationConfigInterface::CONFIG_BUFFER);
            }

            if(!isset($data[ReservationConfigInterface::PRODUCT_SKU]) || empty($data[ReservationConfigInterface::PRODUCT_SKU])) {
                $data[ReservationConfigInterface::PRODUCT_SKU] = '';
                if(!isset($data['file'])) {

                    if ($data[ReservationConfigInterface::FILTER] === ReservationConfigInterface::FILTER_PRODUCT || $data[ReservationConfigInterface::FILTER] === ReservationConfigInterface::FILTER_ALL) {
                        $this->messageManager->addErrorMessage('Product SKU is required.');
                        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                        return $resultRedirect;
                    }
                }
            }

            if ($data[ReservationConfigInterface::FILTER] === ReservationConfigInterface::FILTER_CATEGORY) {
                $data[ReservationConfigInterface::PRODUCT_SKU] = '';
            } else if ($data[ReservationConfigInterface::FILTER] === ReservationConfigInterface::FILTER_PRODUCT) {
                unset($data['data']['category_ids']);
                $data[ReservationConfigInterface::CATEGORY_ID] = '';
            } else if ($data[ReservationConfigInterface::FILTER] === ReservationConfigInterface::FILTER_STORE) {
                if(empty($data[ReservationConfigInterface::STORE_CODE])) {
                    $this->messageManager->addErrorMessage('Store is required if filter by store.');
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }

                unset($data['data']['category_ids']);
                $data[ReservationConfigInterface::PRODUCT_SKU] = '';
                $data[ReservationConfigInterface::CATEGORY_ID] = '';
            }

            $validateValue = $this->validateValue($data[ReservationConfigInterface::VALUE], $data[ReservationConfigInterface::CONFIG]);
            if($validateValue['result'] == false) {
                $this->messageManager->addErrorMessage($validateValue['message']);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }

            if(!empty($data[ReservationConfigInterface::PRODUCT_SKU])) {
                $data[ReservationConfigInterface::PRODUCT_SKU] = $this->generateProductSkus($data[ReservationConfigInterface::PRODUCT_SKU]);

                $validateProduct = $this->validateProduct($data[ReservationConfigInterface::PRODUCT_SKU], $data[ReservationConfigInterface::CONFIG]);
                if($validateProduct['result'] == false) {
                    $this->messageManager->addErrorMessage($validateProduct['message']);
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
            }

            if(!empty($data['data']['category_ids'])) {
                $data[ReservationConfigInterface::CATEGORY_ID] = $this->json->serialize($data['data']['category_ids']);
                unset($data['data']);

                $categoryIds = $data[ReservationConfigInterface::CATEGORY_ID];
                if(!is_array($categoryIds)) {
                    $categoryIds = $this->json->unserialize($data[ReservationConfigInterface::CATEGORY_ID]);
                }

                $validateCategory = $this->validateCategory($categoryIds, $data[ReservationConfigInterface::CONFIG]);
                if($validateCategory['result'] == false) {
                    $this->messageManager->addErrorMessage($validateCategory['message']);
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
            }

            if(isset($data[ReservationConfigInterface::STORE_CODE])) {
                $data[ReservationConfigInterface::STORE_CODE] = is_array($data[ReservationConfigInterface::STORE_CODE]) ? $this->json->serialize($data[ReservationConfigInterface::STORE_CODE]) : $data[ReservationConfigInterface::STORE_CODE];
            } else {
                $data[ReservationConfigInterface::STORE_CODE] = '';
            }

            $model = $this->initConfig();

            $model->setConfig($data[ReservationConfigInterface::CONFIG]);
            $model->setTitle($data[ReservationConfigInterface::TITLE]);
            $model->setValue($data[ReservationConfigInterface::VALUE]);
            $model->setFlag($data[ReservationConfigInterface::FLAG]);
            $model->setFilter($data[ReservationConfigInterface::FILTER]);
            $model->setCategoryId($data[ReservationConfigInterface::CATEGORY_ID]);
            $model->setProductSku($data[ReservationConfigInterface::PRODUCT_SKU]);
            $model->setStoreCode($data[ReservationConfigInterface::STORE_CODE]);

            try {
                $this->logger->info("===========Start Save date config=============");
                $this->configRepository->save($model);

                if(isset($data['file'])) {
                    $skuBulk = $this->skuBulk->create();
                    $skuBulk->setDataPost($data);
                    $skuBulk->setDataConfig($model);
                    $skuBulk->execute();
                }

                $this->messageManager->addSuccessMessage(__('You saved the date config.'));
                $this->logger->info("success");
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->info("Error save date config. Message = " . $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the date config.'));
                $this->logger->info("Error save date config. Message = " . $e->getMessage());
            }

            $this->logger->info("===========End Save date config=============");

            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
