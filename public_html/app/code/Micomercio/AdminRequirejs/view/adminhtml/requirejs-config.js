var config = {
    paths: {
        'js-cookie/js.cookie': 'js-cookie/js.cookie',
        'js-cookie/cookie-wrapper': 'js-cookie/cookie-wrapper',
        'jquery/jquery-cookie': 'js-cookie/cookie-wrapper',
        'js-storage': 'lib/js-storage/js.storage',
        'js-storage/storage-wrapper': 'lib/js-storage/storage-wrapper',
        'jquery/jquery-storageapi': 'jquery/jquery.storageapi.min',
        'vimeo/vimeo-wrapper': 'Magento_PageBuilder/js/vimeo/vimeo-wrapper',
        'vimeo/player': 'https://player.vimeo.com/api/player'
    },
    shim: {
        'vimeo/player': {
            exports: 'Vimeo'
        }
    }
};
