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

namespace CTCD\Inspiration\Block\Adminhtml\Inspiration\Edit\Buttons;

use Magento\Backend\Block\Widget\Context;

class Generic
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \CTCD\Inspiration\Api\InspirationRepositoryInterface
     */
    protected $repository;

    /**
     * @param Context $context
     * @param \CTCD\Inspiration\Api\InspirationRepositoryInterface $repository
     */
    public function __construct(
        Context $context,
        \CTCD\Inspiration\Api\InspirationRepositoryInterface $repository
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
