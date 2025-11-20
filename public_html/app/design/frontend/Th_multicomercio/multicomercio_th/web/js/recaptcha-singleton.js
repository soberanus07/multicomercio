define([], function () {
  'use strict';

  var loadPromise = null;

  return function (url) {
    // Si ya está cargando o cargado, devuelve la misma promesa
    if (loadPromise) return loadPromise;

    loadPromise = new Promise(function (resolve) {
      // ¿Ya existe un <script> de recaptcha en el DOM?
      var exists = document.querySelector('script[src*="www.google.com/recaptcha/api.js"]');
      if (exists) { resolve(); return; }

      var s = document.createElement('script');
      // Respeta la URL que pasa Magento (con ?render=...&hl=...), si viene
      s.src = url || 'https://www.google.com/recaptcha/api.js?render=explicit&hl=es-419';
      s.async = true;
      s.defer = true;
      s.onload = resolve;
      document.head.appendChild(s);
    });

    return loadPromise;
  };
});
