define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    return function () {
        var $modal = $('#product-notify-modal');
        var $form = $('#product-notify-form');
        var $success = $('#product-notify-success');

        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: 'Avísame cuando llegue',
            buttons: [],
            closed: function () {
                $form[0].reset();
                $form.show();
                $success.hide();
            }
        };

        modal(options, $modal);

        $('.notify-trigger').on('click', function () {
            $modal.modal('openModal');
        });

        $form.on('submit', function (e) {
            e.preventDefault();
            var data = $form.serialize();
            var url = $form.attr('action');

            $.post(url, data)
                .done(function () {
                    $form.hide();
                    $success.fadeIn();

                    // Disparar conversión solo si el envío fue exitoso
                    if (typeof gtag === 'function') {
                        console.log('[Google Ads] Conversión enviada');
                        gtag('event', 'conversion', {
                            'send_to': 'AW-16991196841/u6nRCJeP9PcaEKmthKY_'
                        });
                    }
                })
                .fail(function () {
                    alert('Ocurrió un error al enviar tu solicitud. Intenta nuevamente.');
                });
        });
    };
});
