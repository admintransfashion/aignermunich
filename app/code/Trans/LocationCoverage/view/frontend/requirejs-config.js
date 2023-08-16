/**
 * Copyright © 2018 EaDesign by Eco Active S.R.L. All rights reserved.
 * See LICENSE for license details.
 */

var config = {

    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-rates-validation-rules': {
                'Trans_LocationCoverage/js/model/shipping-rates-validation-rules': true
            },
            'Magento_Customer/js/addressValidation': {
                'Trans_LocationCoverage/js/action/set-shipping-information-customer': true
            },
            'Magento_Checkout/js/action/create-shipping-address': {
                'Trans_LocationCoverage/js/action/create-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'Trans_LocationCoverage/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'Trans_LocationCoverage/js/action/set-billing-information-mixin': true
            },
            "Magento_Checkout/js/view/shipping" : {
                "Trans_LocationCoverage/js/view/shipping": true
            },
            "Magento_Checkout/js/view/billing-address" : {
                "Trans_LocationCoverage/js/view/billing-address": true
            },
            'Magento_Checkout/js/model/shipping-rate-processor/new-address': {
                'Trans_LocationCoverage/js/model/shipping-rate-processor/new-address': true
            }
        }
    }
};