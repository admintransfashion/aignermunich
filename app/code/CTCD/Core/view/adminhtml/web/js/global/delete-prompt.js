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

define(["Magento_Ui/js/modal/prompt", "mage/dataPost"], function (
    prompt,
    dataPost
) {
    "use strict";

    /**
     * Set of a temporary methods used to provide
     * backward compatability with a legacy code.
     */
    window.setLocation = function (url) {
        window.location.href = url;
    };

    /**
     * Helper for onclick action.
     * @param {String} message
     * @param {String} url
     * @param {Object} postData
     * @returns {boolean}
     */
    window.deletePrompt = function (title, message, passkey, url, postData) {
        prompt({
            content: message,
            title: title,
            actions: {
                confirm: function (value) {
                    if (passkey && value == passkey) {
                        if (postData !== undefined) {
                            postData.action = url;
                            dataPost().postData(postData);
                        } else {
                            setLocation(url);
                        }
                    } else {
                        alert("Invalid input!");
                    }
                },
            },
        });

        return false;
    };
});
