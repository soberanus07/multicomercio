var config = {
    map: {
        '*': {
            'chilexpressfront':  'ChilexpressOficial_Chilexpress/js/chilexpressfront',
            'edit_address':  'ChilexpressOficial_Chilexpress/js/edit_address',
        }
    },
    "shim":{
        'chilexpressfront':{
            deps:[
                'jquery',
                'Magento_Checkout/js/model/quote',
                'Magento_Checkout/js/model/shipping-rate-processor/new-address',
                'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
                'Magento_Checkout/js/model/shipping-rate-registry',
            ]
        },
        'edit_address':{
            deps:[
                'jquery',
                'Magento_Checkout/js/model/quote',
                'Magento_Checkout/js/model/shipping-rate-processor/new-address',
                'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
                'Magento_Checkout/js/model/shipping-rate-registry',
            ]
        }
    }
};