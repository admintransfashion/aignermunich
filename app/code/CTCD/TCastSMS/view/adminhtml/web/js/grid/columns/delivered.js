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
            if (row.delivered === '0') {
                return 'status-pending';
            }else if(row.delivered === '1') {
                return 'status-success';
            }else if(row.delivered === '2') {
                return 'status-failed';
            }
            return 'status-pending';
        }
    });
});