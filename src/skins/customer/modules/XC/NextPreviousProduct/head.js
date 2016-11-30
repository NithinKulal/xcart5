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
    data.widget.base.find('.product-cell .product.next-previous-disabled a').addClass('next-previous-disabled');

    data.widget.base
      .find('.product-cell a, .product-cell .quicklook-link')
      .not('.next-previous-assigned')
      .off('click.cookieset')
      .on('click.cookieset',
        function(event) {
          var date = new Date();
          date.setTime(date.getTime() + 30 * 60 * 1000);
          var expires = "; expires=" + date.toUTCString();
          var path = '';

          var box = jQuery(this).parents('.product-cell').find('.next-previous-cookie-data').eq(0);

          var productId = box.data('xcProductId');
          var dataString = box.data('xcNextPrevious');
          dataString['created'] = date.getTime();

          if (jQuery(this).hasClass('next-previous-disabled')) {
            dataString['disabled'] = true;
          }

          if (xliteConfig.npCookiePath) {
            path = '; path=' + xliteConfig.npCookiePath;
          }

          document.cookie = 'xc_np_product_' + productId + '=' + JSON.stringify(dataString) + path + expires;
        }
      )
      .addClass('next-previous-assigned');
  }
);

var nextPreviousDisable = function (wrapper) {

  var attach = function (wrapper) {
    jQuery(wrapper)
      .find('a')
      .not('.next-previous-assigned')
      .on(
        'click',
        function () {
          var date = new Date();
          date.setTime(date.getTime() + 30 * 60 * 1000);
          var expires = "; expires=" + date.toUTCString();
          var path = '';

          if (xliteConfig.npCookiePath) {
            path = '; path=' + xliteConfig.npCookiePath;
          }

          document.cookie = 'xc_np_disable=1' + path + expires;
        }
      )
      .addClass('next-previous-assigned');
  };

  attach(wrapper);
  core.microhandlers.add('disableNp' + wrapper, wrapper, attach);
};

core.bind('load', function () {
  nextPreviousDisable('.lc-minicart .item-name');
  nextPreviousDisable('#shopping-cart .selected-product');
});
