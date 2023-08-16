<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Widgets
 * @license  Proprietary
 *
 * @author   Ashadi <ashadi.sejati@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Gtm\Block\Html;

class Footer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Trans\Gtm\Helper\Data
     */
    protected $gtmHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Trans\Gtm\Helper\Data $gtmHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\Customer\Model\Session $customerSession,
        \Trans\Gtm\Helper\Data $gtmHelper,
        array $data = []
    )
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->customerSession = $customerSession;
        $this->gtmHelper = $gtmHelper;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * unset Flag login succes for datalayer
     *
     * @return string
     */
    public function unsFlagAfterLogin()
    {
        return $this->customerSession->unsFlagAfterLogin();
    }

    /**
     * get customer id
     * @return string
     */
    public function getCustomerIdCustom()
    {
        return $this->gtmHelper->getCustomerIdCustom();
    }

    /**
     * get customer id
     * @return string
     */
    public function getCurrentCustomerId()
    {
        return $this->gtmHelper->getCurrentCustomerId();
    }

    /**
     * Retrieve datalayer register from customer session
     * References:
     * \Trans\Customer\Plugin\Customer\Controller\Account\LoginPost::aroundExecute
     *
     * @return string
     */
    public function getDataLayerLoginSuccess()
    {
        $dataLayerString = false;
        if($this->customerSession->getFlagAfterLogin() == true){
            if($this->getCustomerIdCustom()){
                $dataLayer = [
                    'event' => 'login_success',
                    'user_id' => $this->getCustomerIdCustom()
                ];
                $dataLayerString = 'dataLayer.push('. $this->jsonSerializer->serialize($dataLayer).');';
            }
        }

        return $dataLayerString;
    }

    /**
     * Retrieve datalayer register from customer session
     * References:
     * \Trans\Gtm\Observer\Registrationcustomerafter::execute
     *
     * @return string
     */
    public function getDataLayerRegister()
    {
        $dataLayer = $this->customerSession->getDataLayerRegister();
        $dataLayerString = false;
        if($dataLayer && !empty($dataLayer)){
            $dataLayerString = 'dataLayer.push('. $this->jsonSerializer->serialize($dataLayer).');';
        }

        return $dataLayerString;
    }

    /**
     * unset datalayer register
     *
     * @return string
     */
    public function unsDataLayerRegister()
    {
        return $this->customerSession->unsDataLayerRegister();
    }
}
