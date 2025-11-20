define(['jquery'], function($) {
    'use strict';

    $(document).on('gallery:loaded', function (e) {
        var fotorama = $('.fotorama').data('fotorama');
        if (!fotorama) return;

        fotorama.data.forEach(function(image) {
            ['thumb', 'img', 'full'].forEach(function(key) {
                if (image[key]) {
                    const webpUrl = image[key].replace(/\.(jpe?g|png)$/i, '.webp');
                    const testImage = new Image();

                    testImage.onload = function () {
                        image[key] = webpUrl;
                        fotorama.load(fotorama.data); // Recargar con WebP
                    };

                    testImage.src = webpUrl;
                }
            });
        });
    });
});