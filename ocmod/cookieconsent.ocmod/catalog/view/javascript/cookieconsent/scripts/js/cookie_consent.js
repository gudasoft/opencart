var consent = consent || {};
consent.cookies = (function () {

    'use_strict';

    // Show element with fade-in
    function fadeIn(el) {
        el.style.opacity = '0';
        el.style.display = 'block';
        var last = +new Date();
        var tick = function () {
            el.style.opacity = +el.style.opacity + (new Date() - last) / 200;
            last = +new Date();
            if (+el.style.opacity < 1) {
                (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
            }
        };
        tick();
    }

    // Hide element with fade-out
    function fadeOut(el) {
        el.style.opacity = '1';
        var last = +new Date();
        var tick = function () {
            el.style.opacity = +el.style.opacity - (new Date() - last) / 200;
            last = +new Date();
            if (+el.style.opacity > 0) {
                (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
            } else {
                el.style.display = 'none';
            }
        };
        tick();
    }

    // Run when the document is ready
    document.addEventListener('DOMContentLoaded', function() {
        if (getCookie('CookieConsent') === null) {
            var el = document.querySelector('.cookie-consent');
            if (el) { fadeIn(el); }
        }
    }, false);

    // Set a cookie
    function setCookie(key, value, days) {
            var expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();

    } // End of the setCookie method

    // Get a cookie
    function getCookie(key) {
        var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
        return keyValue ? keyValue[2] : null;
    } // End of getCookie method

    // Public methods
    return {
        // Set cookie consent
        setCookieConsent: function () {
            setCookie('CookieConsent', true, 360);
            var el = document.querySelector('.cookie-consent');
            if (el) { fadeOut(el); }

        } // End of the setCookieConsent method
    };

})();
