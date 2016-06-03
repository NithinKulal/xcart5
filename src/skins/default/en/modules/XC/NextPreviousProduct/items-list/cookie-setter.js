/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * NextPreviousProduct items list cookie setter
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind(
  'list.products.postprocess',
  function(event, data) {
    data.widget.base
      .find('.product-cell a')
      .not('.quicklook-link')
      .not('.next-previous-assigned')
      .off('click')
      .click(
        function(event) {
          var date = new Date();
          date.setTime(date.getTime() + 30 * 60 * 1000);
          var expires = "; expires=" + date.toUTCString();
          var path = '';

          var box = jQuery(this).parents('.product-cell').find('.next-previous-cookie-data').eq(0);

          var productId = box.data('xcProductId');
          var dataString = box.data('xcNextPrevious');
          dataString['created'] = date.getTime();

          if (box.data('xcCookiePath')) {
            path = '; path=' + box.data('xcCookiePath');
          }

          document.cookie = 'xc_np_product_' + productId + '=' + JSON.stringify(dataString) + path + expires;
        }
      )
      .addClass('next-previous-assigned');
  }
);
