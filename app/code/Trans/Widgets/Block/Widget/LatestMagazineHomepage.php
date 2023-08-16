<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Widgets
 * @license  Proprietary
 *
 * @author   HaDi <ashadi.sejati@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Widgets\Block\Widget;

class LatestMagazineHomepage extends \Trans\Widgets\Block\Widget\AbstractWidget
{
    protected $_template = "widget/latestmagazinehomepage.phtml";

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Mageplaza\Blog\Helper\Data
     */
    protected $blogHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \CTCD\Core\Helper\Url $ctcdCoreUrlHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mageplaza\Blog\Helper\Data $blogHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \CTCD\Core\Helper\Url $ctcdCoreUrlHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mageplaza\Blog\Helper\Data $blogHelper,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->blogHelper = $blogHelper;
        parent::__construct($context, $ctcdCoreUrlHelper, $data);
    }

    /**
     * Get latest post of blog
     *
     * @return \Mageplaza\Blog\Model\ResourceModel\Post\Collection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLatestPost()
    {
        $latestPost = null;
        $posts = $this->blogHelper->getPostList($this->storeManager->getStore()->getId());
        $posts->getSelect()->order('publish_date DESC')->limit(1);
        if($posts){
            $latestPost = $posts->getFirstItem();
        }
        return $latestPost;
    }

    /**
     * find /n in text
     *
     * @param $description
     *
     * @return string
     */
    public function maxShortDescription($description)
    {
        if (is_string($description)) {
            $html = '';
            foreach (explode("\n", trim($description)) as $value) {
                $html .= '<p>' . $value . '</p>';
            }

            return $html;
        }

        return $description;
    }

    /**
     * Resize Image Function
     *
     * @param $image
     * @param null $size
     * @param string $type
     *
     * @return string
     */
    public function resizeImage($image, $size = null, $type = \Mageplaza\Blog\Helper\Image::TEMPLATE_MEDIA_TYPE_POST)
    {
        return $this->blogHelper->getImageHelper()->resizeImage($image, $size, $type);
    }
}
