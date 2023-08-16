/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Reservation
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

define(
    [
        'jquery',
        'mage/url',
        'Trans_Reservation/js/action/reserve',
        'Magento_Customer/js/customer-data',
        'mage/validation',
        'Magento_Ui/js/modal/modal'
    ],
    function($, url, reserve, customerData) {
        "use strict";
        //creating jquery widget
        $.widget('Reservation.Popup', {
            options: {
                modalForm: '#popup',
                modalButton: '#product-reserve-button'
            },

            _create: function() {
                this.options.modalOption = this.getModalOptions();
                this._bind();
                $('.reserve-product-store').on('click', function(){
                });
                $(document).on('click', '.message-reservation', function(){
                    $('.message-reservation').remove();
                });
            },

            getModalOptions: function() {
                /** * Modal options */
                var getCustomerId = $('#getCustomerHashEmail').data("hashemail");
                var options = {
                    type: 'popup',
                    responsive: true,
                    clickableOverlay: false,
                    title: $.mage.__('ADDED TO RESERVATION'),
                    modalClass: 'popup',
                    buttons: [
                        {
                            text: $.mage.__('Reserve More'),
                            class: 'reseve-more',
                            click: function () {
                                this.closeModal();
                                dataLayer.push({
                                    'event': 'reserve_more',
                                    'user_id': ''+getCustomerId+''
                                });

                            }
                        },
                        {
                            text: $.mage.__('Complete Reservation'),
                            class: 'complete-reserve',
                            click: function () {
                                this.closeModal();
                                window.location.href = url.build('reservation/cart/index');
                            }
                        }
                    ]
                };
                return options;
            },

            _bind: function(){
                var modalOption = this.options.modalOption;
                var modalForm = this.options.modalForm;
                var form = $('#product_addtocart_form'), sku, optionSelected, contentUrl, validate, swatchObj, attributeId;
                $(document).on('click', this.options.modalButton, function(){
                    validate = form.validation() && form.validation('isValid');

                    if(validate == false) {
                        return false;
                    }

                    contentUrl = url.build('reservation/cart/ForceAddToCart');
                    sku = form.data().productSku;

                    optionSelected = $('input[name="selected_configurable_option"]').val();

                    swatchObj = $('.swatch-attribute');
                    attributeId = swatchObj.attr('attribute-id');
                    optionSelected = swatchObj.attr('option-selected');

                    $('body').trigger('processStart');

                    setTimeout(function(){
                        $.ajax({
                            context: '#reservation-list',
                            url: contentUrl,
                            data: {sku: sku, optionSelected: optionSelected, attributeId: attributeId},
                            // showLoader: true,
                            type: "POST",
                        }).done(function (data) {
                            $('body').trigger('processStop');
                            if(data.status == 'error') {
                                //     customerData.set('messages', {
                                //     messages: [{
                                //         type: 'error',
                                //         text: data.message
                                //     }]
                                // });

                                $(".message-reservation").remove();
                                $('.page.messages').append("<div class='message-reservation message-error error message'><div>"+data.message+"</div></div>");
                                return false;
                            } else {
                                $('#reservation-list').html(data.output);
                                $(modalForm).modal(modalOption);
                                $(modalForm).trigger('openModal');
                                var dataSize = data.size ?  data.size : '';
                                var dataColor = data.color ?  data.color : '';

                                dataLayer.push({
                                    'event': 'addToCart',
                                    'product_size': ''+dataSize+'',
                                    'product_for': ''+data.product_for+'',
                                    'user_id': ''+data.id+'',
                                    'ecommerce': {
                                        'currencyCode': 'IDR',
                                        'add': {
                                            'products': [{
                                                'name': ''+data.product_name+'',
                                                'id': ''+data.product_id+'',
                                                'price': ''+data.price+'',
                                                'brand': 'Aigner',
                                                'category': ''+data.category+'',
                                                'variant': ''+dataColor+'',
                                                'quantity': ''+data.qty+''
                                            }]}}});
                                return true;
                            }
                        });
                    },2000);

                });
            }
        });

        return $.Reservation.Popup;
    }
);
