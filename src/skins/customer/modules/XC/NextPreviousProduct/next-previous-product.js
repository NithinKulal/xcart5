/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * NextPreviousProduct product page cookie setter
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var nextPreviousLinkHoverHandler = function () {
  jQuery(this).find('.next-previous-dropdown').fadeIn(200);
};

var nextPreviousLinkUnhoverHandler = function () {
  jQuery(this).find('.next-previous-dropdown').fadeOut(200);
};

core.microhandlers.add(
  'NextPreviousLinkHandler',
  '.next-previous-link',
  function () {
    jQuery(this).hover(nextPreviousLinkHoverHandler, nextPreviousLinkUnhoverHandler);

    jQuery(this).find('a').click(function () {
      var date = new Date();
      date.setTime(date.getTime()+30*60*1000);
      var expires = "; expires="+date.toUTCString();
      var path = '';

      var box = jQuery(this).parents('.next-previous-link').find('.next-previous-cookie-data').eq(0);

      var productId = box.data('xcProductId');
      var dataString = box.data('xcNextPrevious');
      dataString['created'] = date.getTime();

      if (xliteConfig.npCookiePath) {
          path = '; path=' + xliteConfig.npCookiePath;
      }

      document.cookie = 'xc_np_product_' + productId + '=' + JSON.stringify(dataString) + path + expires;
    });
  }
);
