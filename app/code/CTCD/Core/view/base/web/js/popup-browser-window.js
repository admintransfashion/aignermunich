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

define([], function () {
    'use strict';

    var defaultValues = {
        width: 500,
        height: 500,
        name: '_blank'
    };

    /**
     * Create a popup window browser with custom width and height
     *
     * @param {string} url
     * @param {string} name
     * @param {number} width
     * @param {number} height
     */
    function popupWindow(url, name, width, height) {
        if (typeof(url) === 'undefined' || url !== ''){
            if (typeof(width) ==='undefined') width = defaultValues.width;
            if (typeof(height) === 'undefined') height = defaultValues.height;
            if (typeof(name) === 'undefined' || name === '') name = defaultValues.name;
            var openedWindow = window.open(url, name, 'width=' + width + ', height=' + height + ', toolbar=no, menubar=no, scrollbars=no, fullscreen=no, resizable=no');
            if (window.focus) openedWindow.focus();
            return openedWindow;
        }
    }

    /**
     * Create a popup window browser in the center/middle of screenwith custom width and height
     *
     * @param {string} url
     * @param {string} name
     * @param {number} width
     * @param {number} height
     */
    function popupCenterWindow(url, name, width, height) {
        if (typeof(url) === 'undefined' || url !== '') {
            if (typeof(width) ==='undefined') width = defaultValues.width;
            if (typeof(height) === 'undefined') height = defaultValues.height;
            if (typeof(name) === 'undefined' || name === '') name = defaultValues.name;

            var dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : screen.left;
            var dualScreenTop = window.screenTop !== undefined ? window.screenTop : screen.top;

            var screenWidth = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            var screenHeight = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            var left = ((screenWidth / 2) - (width / 2)) + dualScreenLeft;
            var top = ((screenHeight / 2) - (height / 2)) + dualScreenTop;

            var openedWindow = window.open(url, name, 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', toolbar=no, menubar=no, scrollbars=no, fullscreen=no, resizable=no, status=no, channelmode=yes, dependent=yes');
            if (window.focus) openedWindow.focus();
            return openedWindow;
        }
    }

    return {
        popupWindow: popupWindow,
        popupCenterWindow: popupCenterWindow
    };
});
