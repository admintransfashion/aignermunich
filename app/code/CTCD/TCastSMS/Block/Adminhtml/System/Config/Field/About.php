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

namespace CTCD\TCastSMS\Block\Adminhtml\System\Config\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class About extends Field
{
    /**
     * @var string
     */
    protected $_template = 'CTCD_TCastSMS::system/config/field/about.phtml';

    /**
	 * @param \CTCD\TCastSMS\Helper\Sender
	 */
	protected $tcastOTPSenderHelper;

    /**
     * @param Context $context
     * @param \CTCD\TCastSMS\Helper\Sender $tcastOTPSenderHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        \CTCD\TCastSMS\Helper\Sender $tcastOTPSenderHelper,
        array $data = []
    ) {
        $this->tcastOTPSenderHelper = $tcastOTPSenderHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        return $this->toHtml();
    }

    /**
     * Get TCastSMS balance
     *
     * @return string
     * 
     */
    public function getCurrentBalance()
    {
        return $this->tcastOTPSenderHelper->getBalanceNominal($this->tcastOTPSenderHelper->requestBalance());
    }
    
}
