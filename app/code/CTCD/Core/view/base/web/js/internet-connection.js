/**
 * Copyright © 2022 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

define([
    'mage/url',
    "globalTimeout"
], function (url, globalTimeout) {
    'use strict';

    /**
     * Testing internet connection status
     * @param {Function|null} successConnection
     * @param {Function|null} internalError
     * @param {Function|null} noConnectionError
     */
    function status(successConnection = null, internalError = null, noConnectionError = null) {
        var xhr = XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHttp');
        xhr.open("GET", url.build('connection/status'),true);
        xhr.setRequestHeader("Cache-Control", "no-cache, no-store, must-revalidate");
        xhr.setRequestHeader("Pragma", "no-cache");
        xhr.setRequestHeader("Expires", "0");
        xhr.timeout = globalTimeout.getConnectionTimeout();
        xhr.send();
        xhr.onload = function() {
            if (xhr.status != 200) {
                if(internalError instanceof Function){
                    internalError();
                }
            } else {
                if(successConnection instanceof Function){
                    successConnection();
                }
            }
        };
        xhr.ontimeout = function(){
            if(noConnectionError instanceof Function){
                noConnectionError();
            }
        }
        xhr.onerror = function(){
            if(noConnectionError instanceof Function){
                noConnectionError();
            }
        }
    }

    return {
        status: status
    };
});
