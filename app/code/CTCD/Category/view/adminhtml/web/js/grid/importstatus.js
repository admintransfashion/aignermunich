/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

define([
    'Magento_Ui/js/grid/columns/column'
], function (Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            bodyTmpl: 'CTCD_Category/grid/cells/importstatus'
        },

        getCustomClass: function (col, row) {
            var htmlClass = 'data-grid-cell-content status-column';
            if(col.index == 'status'){
                if(row.status == 'Success') {
                    htmlClass += ' status-success';
                } else if(row.status == 'Failed') {
                    htmlClass += ' status-failed';
                } else if(row.status == 'Progress') {
                    htmlClass += ' status-progress';
                } else {
                    htmlClass += ' status-pending';
                }
            }

            return htmlClass;
        }
    });
});
