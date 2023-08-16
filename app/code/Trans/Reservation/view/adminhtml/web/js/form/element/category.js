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
    'Magento_Ui/js/modal/modal',
    'Magento_Catalog/js/components/new-category'
], function ($, _, uiRegistry, select, modal, category) {
    'use strict';
    return category.extend({

        initialize: function (){
            this._super();

            var filter = uiRegistry.get('index = filter');

            if(filter.value() === 'product') {
                this.hide();
            }

            if(filter.value() === 'store') {
                this.hide();
            }

            return this;

        }
    });
});
