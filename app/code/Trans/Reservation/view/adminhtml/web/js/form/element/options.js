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

define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function ($, _, uiRegistry, select, modal) {
    'use strict';
    return select.extend({

        initialize: function (){
            this._super();
            var fieldCategory = uiRegistry.get('index = category_ids');
            var sampleFile = uiRegistry.get('index = sampleFile');
            var fieldProduct = uiRegistry.get('index = product_skus');
            var fieldUpload = $('input[name="bulk"]');

            var filter = this._super().initialValue;

            if (filter == 'category') {
                if (typeof fieldCategory !== 'undefined') {
                    fieldCategory.show();
                }

                if (typeof fieldProduct !== 'undefined') {
                    fieldProduct.visible = false;
                }

                fieldUpload.hide();
            } else if(filter == 'product') {
                if (typeof fieldCategory !== 'undefined') {
                    fieldCategory.hide();
                }

                // if (typeof fieldProduct !== 'undefined') {
                //     fieldProduct.parent().show();
                // }
            } else if(filter == 'all') {
                if (typeof fieldCategory !== 'undefined') {
                    fieldCategory.show();
                }

                // if (typeof fieldProduct !== 'undefined') {
                //     fieldProduct.parent().show();
                // }
            } else {
                if (typeof fieldCategory !== 'undefined') {
                    fieldCategory.hide();
                }

                if (typeof fieldProduct !== 'undefined') {
                    fieldProduct.visible = false;
                    // fieldUpload.hide();
                }
                fieldUpload.hide();
            }

            return this;
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var sampleFile = uiRegistry.get('index = sampleFile');
            var fieldCategory = uiRegistry.get('index = category_ids');
            var fieldUpload = $('input[name="file"]').parent().parent().parent().parent();
            var fieldProduct = $('div[data-index="product_skus"]');
            var filter = value;

            // console.log(fieldUpload.parent().parent().parent().parent());

            if (filter == 'category') {
                fieldCategory.show();
                fieldUpload.hide();
                sampleFile.hide();
                fieldProduct.parent().hide();
                // fieldProduct.hide();
            } else if(filter == 'product') {
                fieldProduct.parent().show();
                fieldUpload.show();
                sampleFile.show();
                fieldCategory.hide();
            } else if(filter == 'all') {
                fieldCategory.show();
                fieldUpload.show();
                sampleFile.show();
                fieldProduct.parent().show();
                // fieldProduct.show();
            } else {
                fieldCategory.hide();
                fieldUpload.hide();
                sampleFile.hide();
                fieldProduct.parent().hide();
                // fieldProduct.hide();
            }

            return this._super();
        },
    });
});
