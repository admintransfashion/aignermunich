/**
 * @author   Hadi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 
 */

define([
    'jquery',
    'mage/mage'
], function ($) {
    'use strict';

    return function (data) {
        $($.mage.redirect(data.url, 'assign', 5000));
        dataLayer.push({
		'event': 'logout',
		'user_id': 'null' 
		});
    };
});
