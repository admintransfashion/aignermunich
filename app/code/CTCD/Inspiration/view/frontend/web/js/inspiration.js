/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

require([
    'jquery',
    'domReady!'
], function ($) {
    $(document).ready(function () {
        $("#inspirationMobileMenu").on('click', function () {
            $(this).next(".inspiration-mobile-items").slideToggle();
            $(this).toggleClass('active');
        });
    });
});
