/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

define([], function () {
    'use strict';

    /**
     * Convert MySQL Date to Javascript Object Date
     * MysQL Datetime -> 2021-06-30
     *
     * @param {string} mysqlDate
     */
    function convertMysqlDate(mysqlDate) {
        var dateReg = /^\d{4}([-])\d{2}\1\d{2}$/
        if (typeof(mysqlDate) !== 'undefined' && mysqlDate !== '' && mysqlDate !== null && mysqlDate.length === 10 && mysqlDate.match(dateReg)){
            let dateParts = mysqlDate.split(/[-]/);
            dateParts[1]--;
            return new Date(...dateParts);
        }
        return '';
    }

    /**
     * Convert MySQL DateTime to Javascript Object Date
     * MysQL Datetime -> 2021-06-30 23:59:59
     *
     * @param {string} mysqlDateTime
     */
    function convertMysqlDateTime(mysqlDateTime) {
        var dateTimeReg = /^\d{4}(-)\d{2}\1\d{2}( )\d{2}(:)\d{2}\3\d{2}$/
        if (typeof(mysqlDateTime) !== 'undefined' && mysqlDateTime !== '' && mysqlDateTime !== null && mysqlDateTime.length === 19 && mysqlDateTime.match(dateTimeReg)){
            let dateTimeParts = mysqlDateTime.split(/[- :]/);
            dateTimeParts[1]--;
            return new Date(...dateTimeParts);
        }
        return '';
    }

    /**
     * Check whether the given MySQL date is valid or not
     * MysQL Date -> 2021-06-30
     *
     * @param {string} mysqlDate
     */
    function isValidMySQLDate(mysqlDate){
        let dateReg = /^((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))$/
        if (typeof(mysqlDate) !== 'undefined' && mysqlDate !== '' && mysqlDate !== null && mysqlDate.length === 10 && mysqlDate.match(dateReg)){
            return true;
        }
        return false;
    }

    /**
     * Check whether the given MySQL datetime is valid or not
     * MysQL Datetime -> 2021-06-30 23:59:59
     *
     * @param {string} mysqlDateTime
     */
    function isValidMySQLDateTime(mysqlDateTime){
        let dateTimeReg = /^((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))(\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))$/
        if (typeof(mysqlDateTime) !== 'undefined' && mysqlDateTime !== '' && mysqlDateTime !== null && mysqlDateTime.length === 19 && mysqlDateTime.match(dateTimeReg)){
            return true;
        }
        return false;
    }

    return {
        convertMysqlDate: convertMysqlDate,
        convertMysqlDateTime: convertMysqlDateTime,
        isValidMySQLDate: isValidMySQLDate,
        isValidMySQLDateTime: isValidMySQLDateTime
    };
});
