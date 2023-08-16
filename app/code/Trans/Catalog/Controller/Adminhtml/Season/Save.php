<?php
/**
 * @category Trans
 * @package  Trans_Catalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Controller\Adminhtml\Season;
 
use Magento\Framework\Exception\LocalizedException;
use Trans\Catalog\Api\Data\SeasonInterface;
use Magento\Framework\Exception\NoSuchEntityException;
 
/**
 * Class Save
 */
class Save extends \Trans\Catalog\Controller\Adminhtml\Season\Season
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Trans_Catalog::season';
 
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        
        if($data) {
            $model = $this->initSeason();
            
            $model->setCode($data['code']);
            $model->setLabel($data['label']);
            $model->setDesc($data['description']);
            $model->setFlag($data['flag']);

            try {
                // $this->logger->info("===========Start Save Season=============");
                $this->seasonRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the Season.'));
                // $this->logger->info("success");
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                // $this->logger->info("Error save season. Message = " . $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the season.'));
                // $this->logger->info("Error save season. Message = " . $e->getMessage());
            }
            
            // $this->logger->info("===========End Save Season=============");

            return $resultRedirect->setPath('*/*/form', ['id' => $model->getId()]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
