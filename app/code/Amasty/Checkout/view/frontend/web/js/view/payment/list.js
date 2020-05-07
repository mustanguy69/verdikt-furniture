// Checkout payment methods view mixin
define([
    'Magento_Checkout/js/model/payment-service',
    'Amasty_Checkout/js/view/utils',
    'Magento_Customer/js/model/customer'
], function (paymentService, viewUtils, customer) {
    'use strict';

    return function (Component) {
        return Component.extend({
            initialize: function () {
                this._super();
		        this.selectedStore = window.checkoutConfig.storeName;
            },

            /**
             * add loader block for payment
             */
            isLoading: paymentService.isLoading,

            getGroupTitle: function (newValue) {
                var paymentMethodLayoutConfig = viewUtils.getBlockLayoutConfig('payment_method');

                if (newValue().index === 'methodGroup'
                    && paymentMethodLayoutConfig !== null
                ) {
                    return paymentMethodLayoutConfig.title;
                }

                return this._super(newValue);
            },

            isLoggedIn: function () {
                return customer.isLoggedIn();
            }

        });
    };
});
