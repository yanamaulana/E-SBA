(function (window, document, $) {
    'use strict';

    // The layout loads jQuery in the head and again through the theme bundle.
    // Register once per jQuery instance so both early and page AJAX are covered.
    if (!$ || $.esbaCsrfInstalled || !window.esbaCsrf) return;
    $.esbaCsrfInstalled = true;

    function token() {
        var prefix = window.esbaCsrf.cookie + '=';
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();
            if (cookie.indexOf(prefix) === 0) {
                return decodeURIComponent(cookie.substring(prefix.length));
            }
        }
        return window.esbaCsrf.hash;
    }

    function sameOrigin(url) {
        try {
            return new URL(url || window.location.href, window.location.href).origin === window.location.origin;
        } catch (error) {
            return false;
        }
    }

    $.ajaxPrefilter(function (options) {
        if (options.type.toUpperCase() !== 'POST' || !sameOrigin(options.url)) return;

        var name = window.esbaCsrf.name;
        var value = token();
        if (window.FormData && options.data instanceof window.FormData) {
            options.data.set(name, value);
        } else if (typeof options.data === 'string') {
            // jQuery serializes ordinary objects before running prefilters.
            // Replace any old hidden-field token instead of sending duplicates.
            var data = new URLSearchParams(options.data);
            data.set(name, value);
            options.data = data.toString();
        } else {
            options.data = options.data || {};
            options.data[name] = value;
            if (options.processData !== false) options.data = $.param(options.data);
        }
    });

    // Native forms have server-rendered fields; refresh them on submit if
    // another tab has renewed the CSRF cookie since this page was opened.
    if (!window.esbaCsrfSubmitInstalled) {
        window.esbaCsrfSubmitInstalled = true;
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (form.method.toUpperCase() !== 'POST' || !sameOrigin(form.action)) return;
            var field = form.elements.namedItem(window.esbaCsrf.name);
            if (!field) {
                field = document.createElement('input');
                field.type = 'hidden';
                field.name = window.esbaCsrf.name;
                form.appendChild(field);
            }
            field.value = token();
        }, true);
    }
})(window, document, window.jQuery);
