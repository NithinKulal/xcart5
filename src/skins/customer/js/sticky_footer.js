/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sticky footer
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function() {
  var browserDetectCache = null;

  function isIE() {
    if (browserDetectCache === null) {
      var
        ua = navigator.userAgent,
        browser = /Edge\/\d+/.test(ua) ? 'ed' : /MSIE 9/.test(ua) ? 'ie9' : /MSIE 10/.test(ua) ? 'ie10' : /MSIE 11/.test(ua) ? 'ie11' : /MSIE\s\d/.test(ua) ? 'ie?' : /rv\:11/.test(ua) ? 'ie11' : /Firefox\W\d/.test(ua) ? 'ff' : /Chrome\W\d/.test(ua) ? 'gc' : /Chromium\W\d/.test(ua) ? 'oc' : /\bSafari\W\d/.test(ua) ? 'sa' : /\bOpera\W\d/.test(ua) ? 'op' : /\bOPR\W\d/i.test(ua) ? 'op' : typeof MSPointerEvent !== 'undefined' ? 'ie?' : '',
        os = /Windows NT 10/.test(ua) ? "win10" : /Windows NT 6\.0/.test(ua) ? "winvista" : /Windows NT 6\.1/.test(ua) ? "win7" : /Windows NT 6\.\d/.test(ua) ? "win8" : /Windows NT 5\.1/.test(ua) ? "winxp" : /Windows NT [1-5]\./.test(ua) ? "winnt" : /Mac/.test(ua) ? "mac" : /Linux/.test(ua) ? "linux" : /X11/.test(ua) ? "nix" : "";

      browserDetectCache = browser.indexOf('ie') === 0;
    }

    return browserDetectCache;
  }

  function repositionStickyFooter() {
    if (isIE()) {
      var footer = document.getElementById('footer-area');
      var space = document.documentElement.scrollHeight - (footer.offsetTop + footer.offsetHeight);

      footer.style.paddingTop = space + 'px';
    }
  }

  repositionStickyFooter();
  window.onresize = repositionStickyFooter;
})();