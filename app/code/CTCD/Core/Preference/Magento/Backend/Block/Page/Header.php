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

namespace CTCD\Core\Preference\Magento\Backend\Block\Page;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Header extends \Magento\Backend\Block\Page\Header
{
    /**
     * @var string
     */
    protected $_template = 'CTCD_Core::magento/backend/page/header.phtml';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \CTCD\Core\Helper\Data
     */
    protected $ctcdCoreHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Request\Http $request
     * @param \CTCD\Core\Helper\Data $ctcdCoreHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        \CTCD\Core\Helper\Data $ctcdCoreHelper,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->ctcdCoreHelper = $ctcdCoreHelper;
        parent::__construct($context, $authSession, $backendData, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function hasLogoImageSrc()
    {
        $logoFile = $this->getCurrentRequest() == 'admin/auth/login' ? $this->ctcdCoreHelper->getCompanyAdminLoginLogo() : $this->ctcdCoreHelper->getCompanyAdminSidebarLogo();
        return $logoFile && $this->ctcdCoreHelper->isUseCustomAdminLogo() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogoImageSrc()
    {
        $mediaUrl = $this ->storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $logoFile = $this->getCurrentRequest() == 'admin/auth/login' ? $this->ctcdCoreHelper->getCompanyAdminLoginLogo() : $this->ctcdCoreHelper->getCompanyAdminSidebarLogo();
        $magentoLogo = $this->getCurrentRequest() == 'admin/auth/login' ? 'images/magento-logo.svg' : 'images/magento-icon.svg';
        return $logoFile && $this->ctcdCoreHelper->isUseCustomAdminLogo() ? $mediaUrl . 'ctcd/core/' . $logoFile : $magentoLogo;
    }

    /**
     * Get title for ALT and TITLE tag on IMG tag
     *
     * @return void
     */
    public function getLogoImageTitle()
    {
        $logoTitle = $this->ctcdCoreHelper->getCompanyLogoTitle();
        return $logoTitle && $this->ctcdCoreHelper->isUseCustomAdminLogo() ? $logoTitle : 'Magento Admin Panel';
    }

    /**
     * Get current request
     *
     * @return string
     */
    protected function getCurrentRequest()
    {
        return $this->request->getModuleName() . '/' .$this->request->getControllerName() . '/' .$this->request->getActionName();
    }
    
}
