<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Widgets
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Widgets\Block\Widget;

class FeaturedProductCarousel extends \Trans\Widgets\Block\Widget\AbstractProductCarousel
{
    const DEFAULT_TITLE  = 'FEATURED PRODUCTS';
    const CLASS_IDENTIFIER = 'featured-product-slider-widget';

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData('title') ? $this->getData('title') : self::DEFAULT_TITLE;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassIdentifier()
    {
        return self::CLASS_IDENTIFIER;
    }
}
