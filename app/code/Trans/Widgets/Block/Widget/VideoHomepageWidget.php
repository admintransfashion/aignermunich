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

class VideoHomepageWidget extends \Trans\Widgets\Block\Widget\AbstractWidget
{
    protected $_template = "widget/videohomepagewidget.phtml";

    public function generateId()
    {
        return date('YmdHis').rand(1000000,9999999);
    }
}
