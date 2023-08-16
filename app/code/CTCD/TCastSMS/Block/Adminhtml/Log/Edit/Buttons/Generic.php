<?php
/**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_TCastSMS
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\TCastSMS\Block\Adminhtml\Log\Edit\Buttons;

use Magento\Backend\Block\Widget\Context;

class Generic
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \CTCD\TCastSMS\Api\LogOtpRepositoryInterface
     */
    protected $repository;

    /**
     * @param Context $context
     * @param \CTCD\TCastSMS\Api\LogOtpRepositoryInterface $repository
     */
    public function __construct(
        Context $context,
        \CTCD\TCastSMS\Api\LogOtpRepositoryInterface $repository
    ) {
        $this->context = $context;
        $this->repository = $repository;
    }

    /**
     * Return Rates ID
     *
     * @return int|null
     */
    public function getDataId()
    {
        $paramId = $this->context->getRequest()->getParam('id', false);
        if(! $paramId) return null;
        try {
            return $this->repository->getById($paramId)->getId();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
