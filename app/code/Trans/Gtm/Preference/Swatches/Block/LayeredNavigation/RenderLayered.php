<?php
/**
 * @category Trans
 * @package  Trans_Gtm
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   ashadi <ashadi.sejati@transdigital.co.id> 
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Gtm\Preference\Swatches\Block\LayeredNavigation;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Framework\View\Element\Template;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;

/**
 * Class RenderLayered Render Swatches at Layered Navigation
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class RenderLayered extends \Magento\Swatches\Block\LayeredNavigation\RenderLayered
{
    /**
     * For `Filterable (with results)` setting
     */
    const FILTERABLE_WITH_RESULTS = '1';

    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'Magento_Swatches::product/layered/renderer.phtml';

    /**
     * @var \Magento\Eav\Model\Attribute
     */
    protected $eavAttribute;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    protected $filter;

    /**
     * @var AttributeFactory
     */
    protected $layerAttribute;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $swatchHelper;

    /**
     * @var \Magento\Swatches\Helper\Media
     */
    protected $mediaHelper;

    /**
     * @var \Magento\Customer\Model\Session
     * @since 100.1.0
     */
    protected $customerSession;

    /**
     * @param Template\Context $context
     * @param Attribute $eavAttribute
     * @param AttributeFactory $layerAttribute
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Swatches\Helper\Media $mediaHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession; 
        parent::__construct($context,$eavAttribute,$layerAttribute,$swatchHelper,$mediaHelper, $data);
    }

     /**
     * get customer id
     * @return string
     */
    public function getCustomerIdCustom()
    {
        //return current customer ID
        if ($this->customerSession->getCustomer()->getEmail()) {
            $hashcustomeremail = hash('sha256', $this->customerSession->getCustomer()->getEmail());
            return $hashcustomeremail;
        }
        return false;
    }
}
