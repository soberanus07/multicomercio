define(['jquery'], function($) {
    'use strict';

    return function(widget) {
        $.widget('mage.gallery', widget, {
            _create: function() {
                this._super();
                this._optimizeLCP();
            },

            _optimizeLCP: function() {
                var self = this;
                
                // Interceptar ANTES de que Fotorama genere las imágenes
                this.element.on('gallery:loaded', function() {
                    self._setLCPAttributes();
                });

                // Aplicar inmediatamente
                this._setLCPAttributes();
            },

            _setLCPAttributes: function() {
                var $images = this.element.find('.fotorama__img');
                
                $images.each(function(index) {
                    var $img = $(this);
                    
                    if (index === 0) {
                        // Primera imagen: LCP optimizado
                        $img.attr({
                            'loading': 'eager',
                            'fetchpriority': 'high'
                        }).removeAttr('decoding');
                    } else {
                        // Resto: lazy loading
                        $img.attr({
                            'loading': 'lazy',
                            'fetchpriority': 'low',
                            'decoding': 'async'
                        });
                    }
                });
            }
        });

        return $.mage.gallery;
    };
});
