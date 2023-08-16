/**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_TCastSMS
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

define([
    'underscore',
    'Magento_Ui/js/grid/columns/select'
], function (_, Column) {
    'use strict';
    return Column.extend({
        defaults: {
            bodyTmpl: 'CTCD_TCastSMS/ui/grid/columns/text-color'
        },
        getTextColor: function (row) {          
            if (row.verified === '0') {
                return 'option-no';
            }else if(row.verified === '1') {
                return 'option-yes';
            }
            return 'option-no';
        }
    });
});