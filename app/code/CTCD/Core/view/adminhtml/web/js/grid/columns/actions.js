/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/actions',
    'Magento_Ui/js/modal/prompt'
], function (Actions, prompt) {
    'use strict';

    return Actions.extend({

        /**
         * Checks if specified action requires a handler function.
         *
         * @param {String} actionIndex - Actions' identifier.
         * @param {Number} rowIndex - Index of a row.
         * @returns {Boolean}
         */
        isHandlerRequired: function (actionIndex, rowIndex) {
            var action = this.getAction(rowIndex, actionIndex);

            return _.isObject(action.callback) || action.confirm || action.prompt || !action.href;
        },

        /**
         * Applies specified action.
         *
         * @param {String} actionIndex - Actions' identifier.
         * @param {Number} rowIndex - Index of a row.
         * @returns {ActionsColumn} Chainable.
         */
        applyAction: function (actionIndex, rowIndex) {
            var action = this.getAction(rowIndex, actionIndex),
                callback = this._getCallback(action);

            if(action.confirm) {
                this._confirm(action, callback);
            }
            else if(action.prompt) {
                this._prompt(action, callback);
            }
            else{
                callback();
            }

            return this;
        },

        /**
         * Shows actions' prompt window.
         *
         * @param {Object} action - Actions' data.
         * @param {Function} callback - Callback that will be
         *      invoked if action is confirmed.
         */
        _prompt: function (action, callback) {
            var promptData = action.prompt;

            prompt({
                title: promptData.title,
                content: promptData.message,
                passkey: promptData.passkey,
                actions: {
                    confirm: function (value) {
                        if(value && value == promptData.passkey){
                            window.location.href = action.href;
                        }
                        else{
                            alert('Invalid input!');
                        }
                        return callback;
                    }
                }
            });
        },
        
    });
});
