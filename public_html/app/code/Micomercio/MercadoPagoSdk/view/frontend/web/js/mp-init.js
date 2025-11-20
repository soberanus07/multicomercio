require(['jquery'], function ($) {
    $(document).ready(function () {
        if (typeof MercadoPago !== 'undefined') {
            const mp = new MercadoPago('APP_USR-33c03199-dd07-41fb-b6b9-ba1f4fc382d9', {
                locale: 'es-CL'
            });
            console.log('MercadoPago SDK v2 cargado correctamente');
            
        } else {
            console.error('MercadoPago SDK v2 no está disponible');
        }
    });
});
