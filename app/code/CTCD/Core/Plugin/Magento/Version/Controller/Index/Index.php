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

namespace CTCD\Core\Plugin\Magento\Version\Controller\Index;

use CTCD\Core\Model\Config\Source\RedirectType;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Index extends \CTCD\Core\Plugin\PluginAbstract
{
    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $forwardFactory;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \CTCD\Core\Helper\Data $ctcdCoreHelper
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \CTCD\Core\Helper\Data $ctcdCoreHelper,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
    ) {
        $this->response = $response;
        $this->redirect = $redirect;
        $this->forwardFactory = $forwardFactory;

        parent::__construct($registry, $urlBuilder, $ctcdCoreHelper);
    }

    public function aroundExecute(
        \Magento\Version\Controller\Index\Index $subject,
        callable $proceed
    ){
        if($this->ctcdCoreHelper->isMagentoVersionBeHidden()) {
            $responseType = $this->ctcdCoreHelper->getResponseTypeOfHidingMagentoVersion();
            if($responseType == RedirectType::RESPONSE_BLANK){
                return;
            }
            elseif($responseType == RedirectType::RESPONSE_REDIRECT_HOMEPAGE){
                $this->redirect->redirect($this->response, '/', []);
                return;
            }
            elseif($responseType == RedirectType::RESPONSE_REDIRECT_404){
                $resultForward = $this->forwardFactory->create();
                $resultForward->setController('index');
                $resultForward->forward('defaultNoRoute');
                return $resultForward;
            }
        }

        return $proceed();
    }
}
