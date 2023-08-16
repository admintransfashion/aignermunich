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

    var timeoutInSecond = {
        connection: 10,
        request: 30
    };

    /**
     * Get global timeout for internet connection action
     */
    function getConnectionTimeout() {
        return timeoutInSecond.connection * 1000;
    }

    /**
     * Get global timeout for request action
     */
    function getRequestTimeout() {
        return timeoutInSecond.request * 1000;
    }

    return {
        getConnectionTimeout: getConnectionTimeout,
        getRequestTimeout: getRequestTimeout
    };
});
