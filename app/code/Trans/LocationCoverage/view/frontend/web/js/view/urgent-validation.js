define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../model/shipping-rates-validator',
        '../model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        translcShippingRatesValidator,
        translcShippingRatesValidationRules
    ) {
        'use strict';
        defaultShippingRatesValidator.registerValidator('translc', translcShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('translc', translcShippingRatesValidationRules);
        return Component;

    }
);