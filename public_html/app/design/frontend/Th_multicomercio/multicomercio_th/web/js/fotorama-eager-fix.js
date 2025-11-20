require(['jquery'], function ($) {
  function forceEagerOnFotoramaImages() {
    $('[data-gallery-role="gallery"] .fotorama__img').each(function () {
      if (this.getAttribute('loading') !== 'eager') {
        this.setAttribute('loading', 'eager');
        console.log('[CLS Fix] Se forzó loading="eager"');
      }
    });
  }

  $(window).on('load', function () {
    // Forzar eager inmediatamente
    forceEagerOnFotoramaImages();

    // Listeners de Fotorama
    $(document).on('fotorama:load fotorama:showend', function () {
      forceEagerOnFotoramaImages();
    });

    // Ocultar placeholder cuando Fotorama esté listo
    $(document).on('fotorama:show', function () {
    console.log('[CLS Fix] fotorama:show detectado — ocultando placeholder');
    $('body').addClass('fotorama-ready');
  });


    // Reintentos programados por seguridad
    [300, 800, 1500, 2500].forEach(function (ms) {
      setTimeout(forceEagerOnFotoramaImages, ms);
    });

    // Observador de mutaciones en la galería
    const gallery = document.querySelector('[data-gallery-role="gallery"]');
    if (gallery) {
      const observer = new MutationObserver(forceEagerOnFotoramaImages);
      observer.observe(gallery, { childList: true, subtree: true });
    }
  });
});
