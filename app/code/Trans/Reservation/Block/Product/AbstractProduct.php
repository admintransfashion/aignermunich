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
namespace Trans\Reservation\Block\Product;

/**
 * Abstract Product
 */
class AbstractProduct extends \Magento\Framework\View\Element\Template
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

	/**
     * @param Context $context
     * @param array $data
     */
    public function __construct(\Magento\Catalog\Block\Product\Context $context, array $data = [])
    {
        $this->coreRegistry = $context->getRegistry();
        parent::__construct($context, $data);
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }
}
