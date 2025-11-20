define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    return function (config, element) {
        $('body').on('ajax:addToCart', function (event, data) {
            if (data && data.productIds && data.productIds.length > 0) {
                var productId = data.productIds[0];
                // Esperar a que customer-data se actualice para obtener precio
                setTimeout(function () {
                    var cart = customerData.get('cart')();
                    var item = cart.items.find(function (el) {
                        return el.product_id == productId;
                    });
                    var value = item ? item.product_price_value : 1.0;
                    var currency = window.checkoutConfig && window.checkoutConfig.quoteData
                        ? window.checkoutConfig.quoteData.quote_currency_code : 'CLP';

                    gtag('event', 'conversion', {
                        'send_to': 'AW-16991196841/ccs6CJa72_gaEKmthKY_',
                        'value': value,
                        'currency': currency
                    });
                }, 1000);
            }
        });
    };
});
