define([
    'jquery'
], function ($) {
    'use strict';

    /**
     *
     * @param {string} message
     * @param {number} showTimeInSec
     * @param {string} textColor
     * @param {string} backColor
     * @param {string} iconUrl
     * @returns {object}
     */
    function show(message, showTimeInSec, textColor, backColor, iconUrl) {
        if($("#setokoFlashMessage").length <= 0) {
            showTimeInSec = (showTimeInSec && parseFloat(showTimeInSec) > 0) ? parseFloat(showTimeInSec) : 3;
            if(! (textColor && (/^#[0-9A-F]{6}$/i.test(textColor)))) {
                textColor = '#FFFFFF';
            }
            if(! (backColor && (/^#[0-9A-F]{6}$/i.test(backColor)))) {
                backColor = '#0F0F0F';
            }
            backColor = 'background-color:' + backColor + ';';
            textColor = 'color:' + textColor + ';';

            let showTime = showTimeInSec * 1000;

            let html = '<div id="setokoFlashMessage" class="setoko-flash-message"><span class="flash-wrapper" style="'+backColor+textColor+'">';
            if(iconUrl) {
                html += '<span class="flash-icon"><img src="'+iconUrl+'" /></span>';
            }
            html += '<span class="flash-message">'+message+'</span></span></div>';

            $('body').append(html);
            setTimeout(function() { $("#setokoFlashMessage").fadeOut(500,function() { $(this).remove(); }); }, showTime);
        }
    }

    return {
        show: show
    };
});
