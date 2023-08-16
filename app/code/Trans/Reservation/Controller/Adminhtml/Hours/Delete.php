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

namespace Trans\Reservation\Controller\Adminhtml\Hours;

use Magento\Framework\Exception\LocalizedException;
use Trans\Reservation\Api\Data\ReservationConfigInterface;

/**
 * Class Delete
 */
class Delete extends \Trans\Reservation\Controller\Adminhtml\Config
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Trans_Reservation::reservation';

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            $model = $this->initConfig();

            try {
                $this->logger->info("=========== Start Delete Reservation Hours =============");
                $this->configRepository->delete($model);
                $this->messageManager->addSuccessMessage(__('You deleted the reservation hours.'));
                $this->logger->info("success");
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->info("Error delete reservation hours. Message = " . $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the reservation hours.'));
                $this->logger->info("Error delete reservation hours. Message = " . $e->getMessage());
            }

            $this->logger->info("=========== End Delete Reservation Hours =============");
        }

        return $resultRedirect->setPath('*/*/');
    }
}
