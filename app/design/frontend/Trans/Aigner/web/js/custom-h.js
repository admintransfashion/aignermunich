/**
 * @author   Hadi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com

 */

// cms sidebar

require([
    'jquery',
    'domReady!'
], function ($) {
    var getCustomerId = $('#navigationIdCustom').data("customerid");
    var textSidebar = $(".sidebar-category-item.is-active").text();
    $(".jsSidebarCategoryDropdownTrigger").html(textSidebar);
    $(".jsSidebarCategoryDropdownTrigger").on('click', function() {
        $(this).next(".sidebar-category-list").slideToggle();
    });


    // scroll sticky pdp
    var didScroll;
    $(window).scroll(function (event) {
        didScroll = true;
    });
    setInterval(function () {
        if (didScroll) {
            if ($('.jsStickyNav').length > 0){
                stickyNav();
            }
            didScroll = false;
        }
    }, 250);
    function stickyNav() {
        var st = $(this).scrollTop();
        var batasButtonScroll = $('.product-add-form .jsProductDetailButton').offset().top + $('.product-add-form .jsProductDetailButton').outerHeight();
        if (st > batasButtonScroll) {
            // Scroll Down
            $(".jsStickyNav").addClass("is-sticky");
            $(".page-header").addClass("is-sticky");
        } else {
            // Scroll Up
            $(".jsStickyNav").removeClass("is-sticky");
            $(".page-header").removeClass("is-sticky");
        }
    }

    // header hallobar
    if ($("#promotion-bar").length > 0) {
        $('body').addClass("has-hellobar");

        $(".close-promotion-bar").on('click', function() {
            $("#promotion-bar").hide();
            $('body').removeClass("has-hellobar");
        });
    }

    // datalayer footer
    $(".footer-categories .footer-link").on('click', function() {
        var getTextFooter = $(this).html();
        var getCustomerId = $("#getCustomerHashEmail").data("hashemail");
        dataLayer.push({
            'event': 'bottom_navigation',
            'menu_name': getTextFooter,
            'user_id': ''+getCustomerId+''
        });
    });

    // datalayer top navigation
    $('.navigation a[data-event="top_navigation"]').on('click', function() {
        var event = $(this).attr('data-event');
        var menu = $(this).attr('data-menu');
        var submenu = $(this).attr('data-submenu');
        var getCustomerId = $("#getCustomerHashEmail").data("hashemail");
        dataLayer.push({
            'event': 'top_navigation',
            'menu_name': ''+menu+'',
            'submenu_name': ''+submenu+'',
            'user_id': ''+getCustomerId+''
        });
    });

    $(".footer-categories .footer-social a").on('click', function() {
        var getScFooter = $(this).attr("title");
        var getCustomerId = $("#getCustomerHashEmail").data("hashemail");
        dataLayer.push({
            'event': 'social_media',
            'social_media': ''+getScFooter+'',
            'user_id': ''+getCustomerId+''
        });

    });

});
