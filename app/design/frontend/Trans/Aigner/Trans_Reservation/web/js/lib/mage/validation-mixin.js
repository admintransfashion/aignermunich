define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {

        $.widget('mage.validation', widget, {
            /**
             * Handle form validation. Focus on first invalid form field.
             *
             * @param {jQuery.Event} event
             * @param {Object} validation
             */
            listenFormValidateHandler: function (event, validation) {
                let firstActive = $(validation.errorList[0].element || []),
                    lastActive = $(validation.findLastActive() ||
                        validation.errorList.length && validation.errorList[0].element || []),
                    parent, windowHeight, successList;

                if (lastActive.is(':hidden')) {
                    parent = lastActive.parent();
                    windowHeight = $(window).height();
                    $('html, body').animate({
                        scrollTop: parent.offset().top - windowHeight / 2
                    });
                }

                // ARIA (removing aria attributes if success)
                successList = validation.successList;

                if (successList.length) {
                    $.each(successList, function () {
                        $(this)
                            .removeAttr('aria-describedby')
                            .removeAttr('aria-invalid');
                    });
                }

                if (firstActive.length) {
                    /* vertically center the first field on the screen. This is best for UX but if you prefer to avoid the scrolling completelly, just remove the next line and the function "scrollToCenterFormFieldOnError" below. */
                    // scrollToCenterFormFieldOnError(firstActive);
                    // firstActive.focus();
                }
            }
        });

        return $.mage.validation;
    }
});