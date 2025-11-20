var config = {
  // Forzamos un loader singleton para reCAPTCHA
   map: {
     '*': {
       'Magento_ReCaptchaFrontendUi/js/reCaptchaScriptLoader': 'js/recaptcha-singleton'
     }
   },

  deps: [
         'js/mobile-accordion'
     ],


    paths: {
        'js-cookie': 'lib/js-cookie/js.cookie',
        'js-cookie/cookie-wrapper': 'lib/js-cookie/cookie-wrapper',
      'jquery/jquery-storageapi': 'jquery/jquery.storageapi.min',
      'vimeo/vimeo-wrapper': 'lib/vimeo/vimeo-wrapper'
    }
};
