/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function(){

  function flyToCart(element) {
    var target = $('.lc-minicart:visible');
    var item = getProductRepresentationFor(element);

    if (target.length && item.element && item.element.length) {
      $(item.element).css('pointer-events', 'none');
      $(item.element).fly(target, {
        view: item.view,
        callback: function () {
          $(item.element).css('pointer-events', '');
        }
      });
    }
  }

  decorate(
    'ProductsListView',
    'addToCart',
    function(element) {
      flyToCart(element);
      return arguments.callee.previousMethod.apply(this, arguments);
    }
  );

  core.bind(
    'popup.open',
    function() {
      $('body > img.photo').remove();
    }
  );

  function isTouchDevice() {
    var
      ua = navigator.userAgent,
      browser = /Edge\/\d+/.test(ua) ? 'ed' : /MSIE 9/.test(ua) ? 'ie9' : /MSIE 10/.test(ua) ? 'ie10' : /MSIE 11/.test(ua) ? 'ie11' : /MSIE\s\d/.test(ua) ? 'ie?' : /rv\:11/.test(ua) ? 'ie11' : /Firefox\W\d/.test(ua) ? 'ff' : /Chrome\W\d/.test(ua) ? 'gc' : /Chromium\W\d/.test(ua) ? 'oc' : /\bSafari\W\d/.test(ua) ? 'sa' : /\bOpera\W\d/.test(ua) ? 'op' : /\bOPR\W\d/i.test(ua) ? 'op' : typeof MSPointerEvent !== 'undefined' ? 'ie?' : '',
      os = /Windows NT 10/.test(ua) ? "win10" : /Windows NT 6\.0/.test(ua) ? "winvista" : /Windows NT 6\.1/.test(ua) ? "win7" : /Windows NT 6\.\d/.test(ua) ? "win8" : /Windows NT 5\.1/.test(ua) ? "winxp" : /Windows NT [1-5]\./.test(ua) ? "winnt" : /Mac/.test(ua) ? "mac" : /Linux/.test(ua) ? "linux" : /X11/.test(ua) ? "nix" : "",
      mobile = /IEMobile|Windows Phone|Lumia/i.test(ua) ? 'w' : /iPhone|iP[oa]d/.test(ua) ? 'i' : /Android/.test(ua) ? 'a' : /BlackBerry|PlayBook|BB10/.test(ua) ? 'b' : /Mobile Safari/.test(ua) ? 's' : /webOS|Mobile|Tablet|Opera Mini|\bCrMo\/|Opera Mobi/i.test(ua) ? 1 : 0,
      tablet = /Tablet|iPad/i.test(ua),
      msGesture = window.navigator && window.navigator.msPointerEnabled && window.MSGesture,
      touch = (( "ontouchstart" in window ) || msGesture || window.DocumentTouch && document instanceof DocumentTouch);

    return touch && (mobile || tablet);
  }

  if (isTouchDevice()) {
    $('.items-list .product-cell .product').addClass('prevent-hover');
  }

  decorate(
    'ProductDetailsView',
    'addProductToCart',
    function() {
      if (!this.base.hasClass('product-quicklook')) {
        flyToCart(this.base.find('form.product-details'));
      }
      return arguments.callee.previousMethod.apply(this, arguments);
    }
  );
})();