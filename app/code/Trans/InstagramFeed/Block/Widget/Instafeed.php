<?php
/**
 * @category Trans
 * @package  Trans_InstagramFeed
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\InstagramFeed\Block\Widget;

/**
 * Class Instafeed
 */
class Instafeed extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Default value for products count that will be shown
     */
    protected $_template = 'widget/instawidget.phtml';

    protected $imageLeft;
    protected $imageMid;
    protected $imageRight;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param $imageLeft
     * @param $imageMid
     * @param $imageRight
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Retrieve image left for widget
     */
    public function getImageLeft()
    {
        $imageLeft = $this->getData('imgsleft');
        return $imageLeft;
    }

    /**
     * Retrieve link left for widget
     */
    public function getLinkLeft()
    {
        $linkLeft = $this->getData('linkleft');
        return $linkLeft;
    }

    /**
     * Retrieve image mid for widget
     */
    public function getImageMid()
    {
        $imageMid = $this->getData('imgsmid');
        return $imageMid;
    }

    /**
     * Retrieve image mid for widget
     */
    public function getLinkMid()
    {
        $linkMid = $this->getData('linkmid');
        return $linkMid;
    }

    /**
     * Retrieve image right for widget
     */
    public function getImageRight()
    {
        $imageRight = $this->getData('imgsright');
        return $imageRight;
    }

    /**
     * Retrieve link right for widget
     */
    public function getLinkRight()
    {
        $linkRight = $this->getData('linkright');
        return $linkRight;
    }
}