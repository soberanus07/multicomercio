define(['jquery'], function ($) {
    'use strict';

    console.log("mobile-accordion.js cargado y ejecutado!");

    $(document).ready(function () {
        // Ocultar todos al inicio
        $('.section-item-content').hide();
        $('.section-item-title').removeClass('is-open');

        // Seleccionar y abrir el primero ("Menú")
        var $firstTitle = $('.section-item-title').first();
        var $firstContent = $firstTitle.next('.section-item-content');

        $firstTitle.addClass('is-open');
        $firstContent.show();

        // Click handler
        $(document).on('click', '.nav-sections-item-switch', function (e) {
            e.preventDefault();

            var $title = $(this).closest('.section-item-title');
            var $content = $title.next('.section-item-content');

            // Cerrar todos
            $('.section-item-title').removeClass('is-open');
            $('.section-item-content').slideUp(200);

            // Abrir actual
            if (!$content.is(':visible')) {
                $title.addClass('is-open');
                $content.stop(true, true).slideDown(200);
            }
        });
    });
});
