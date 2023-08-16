
define([
    "jquery"
], function($) {
    "use strict";
    return function (config, element) {
    	var getHashcustomerEmail = $("#getCustomerHashEmail").data('hashemail');
        $(element).html(getHashcustomerEmail);
    };
});