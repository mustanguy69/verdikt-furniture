define(
    [
        'underscore',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Magento_Checkout/js/model/address-converter'
    ],
    function (_, validationRules, addressConverter) {
        'use strict';

        /**
         * Return cache string by guest address.
         * Improve guest cache string key generation.
         * Collecting by shipping carriers required fields.
         *
         * @param {Object} address - quote model address
         * @return {String}
         * @since 3.0.0
         */
        function getAddressCacheKey (address) {
            var fields,
                formAddress,
                cacheKey = '';

            if (address.getType() !== 'new-address' || address.getType() !== 'new-customer-address') {
                return address.getCacheKey();
            }

            fields = validationRules.getObservableFields();
            formAddress = addressConverter.quoteAddressToFormAddressData(address);
            _.each(fields, function (name) {
                if (formAddress.hasOwnProperty(name)) {
                    cacheKey += '|' + formAddress[name];
                } else if(formAddress['custom_attributes']) {
                    cacheKey += '|' + formAddress['custom_attributes'][name];
                }
            });

            return cacheKey;
        }

        return getAddressCacheKey;
    }
);
