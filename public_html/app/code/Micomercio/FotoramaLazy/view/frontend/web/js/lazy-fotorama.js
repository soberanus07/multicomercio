define(['jquery'], function ($) {
  'use strict';

  function tune($root) {
    try {
      // Slides (stage)
      $root.find('.fotorama__stage__frame').each(function () {
        var frame = this;
        var isActive = frame.classList.contains('fotorama__active');
        var img = frame.querySelector('.fotorama__img, img');

        if (!img) return;

        if (isActive) {
          // LCP: ensure eager and high priority
          img.removeAttribute('loading'); // eager by default
          img.setAttribute('fetchpriority', 'high');
          img.setAttribute('decoding', 'async');
        } else {
          // Non-visible slides: lazy & low priority
          img.setAttribute('loading', 'lazy');
          img.setAttribute('fetchpriority', 'low');
          img.setAttribute('decoding', 'async');
        }
      });

      // Thumbnails (if present) -> always lazy
      $root.find('.fotorama__nav__shaft img, .fotorama__nav__shaft .fotorama__img').each(function () {
        this.setAttribute('loading', 'lazy');
        this.setAttribute('decoding', 'async');
        this.setAttribute('fetchpriority', 'low');
      });

    } catch (e) {
      // Fail-safe: don't break PDP
      // console.warn('[FotoramaLazy] tune error:', e);
    }
  }

  // Run on init and on slide change
  $(document).on('fotorama:ready fotorama:show', '.fotorama', function () {
    tune($(this));
  });

  // First pass in case Fotorama is already mounted
  $(function () {
    $('.fotorama').each(function () {
      tune($(this));
    });
  });
});
