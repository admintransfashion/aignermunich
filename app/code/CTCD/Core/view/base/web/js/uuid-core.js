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

    /**
     * Generate transaction ID
     *
     * @returns {string}
     */
    function getUniqueID() {
        let currentDate = generateCurrentDate();
        return currentDate[0] + generateRandomString() + currentDate[1] + Math.floor(100000 + Math.random() * 999999);
    }

    /**
     * Generate Current Date
     *
     * @returns {string[]}
     */
    function generateCurrentDate()
    {
        let today = new Date();
        let date = today.getDate().toString().padStart(2, "0");
        let month = (today.getMonth()+1).toString().padStart(2, "0");
        let year = today.getFullYear().toString().slice(-2);

        let hour = today.getHours().toString().padStart(2, "0");
        let min = today.getMinutes().toString().padStart(2, "0");
        let sec = today.getSeconds().toString().padStart(2, "0");

        return [year + month + date, hour + min + sec];
    }

    /**
     * Generate random string
     *
     * @returns {string}
     */
    function generateRandomString() {
        let allowedChars ='abcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for ( var i = 0; i < 12; i++ ) {
            result += allowedChars.charAt(Math.floor(Math.random() * allowedChars.length));
        }
        return result;
    }

    return {
        getUniqueID: getUniqueID
    };
});
