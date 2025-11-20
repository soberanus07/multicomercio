define(['jquery'], function ($) {
    'use strict';

    return function () {
        $(document).ready(function () {
            // Quita el role="menu" del <ul>
            $('ul[role="menu"]').removeAttr('role');

            // Quita el role de cada <li class="ui-menu-item">
            $('li.ui-menu-item').removeAttr('role');
        });
    };
});
