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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use CTCD\Inspiration\Api\Data\InspirationInterface;

class Delete extends \CTCD\Inspiration\Controller\Adminhtml\Inspiration
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $inspirationId = $this->getRequest()->getParam('id');
        if ($inspirationId) {
            try {
                $inspiration = $this->inspirationRepository->getById($inspirationId);
                $inspirationTitle = $inspiration->getData(InspirationInterface::TITLE);
                $inspirationKey = $inspiration->getData(InspirationInterface::URL_KEY);
                $this->inspirationRepository->delete($inspirationKey);
                $this->messageManager->addSuccessMessage(__("Inspiration \"%1\" has been deleted successfully.", $inspirationTitle));
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__("Inspiration no longer exists."));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('ctcdinspiration/inspiration/edit', ['id' => $inspirationId]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the inspiration data'));
                return $resultRedirect->setPath('ctcdinspiration/inspiration/edit', ['id' => $inspirationId]);
            }
        }
        else{
            $this->messageManager->addErrorMessage(__('We can\'t find a inspiration data to delete.'));
        }

        $resultRedirect->setPath('ctcdinspiration/*/');
        return $resultRedirect;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CTCD_Inspiration::inspiration_delete');
    }
}
