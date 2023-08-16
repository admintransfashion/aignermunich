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
    'Magento_Ui/js/form/element/file-uploader'
], function ($, _, uiRegistry, uploader) {
    'use strict';
    return uploader.extend({

        initialize: function (){
            this._super();

            var filter = uiRegistry.get('index = filter');

            if(filter.value() === 'product') {
                this.show();
            }

            if(filter.value() === 'all') {
                this.show();
            }

            return this;

        }
    });
});
