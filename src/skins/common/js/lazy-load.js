/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Lazy load
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function processLazyLoadImages() {
  $('.lazy-load').each(function () {
    var image = $(this).find('img:first');
    var wrapper = $(this);
    wrapper.addClass('lazy-load-transition');
    image.load(function () {
      wrapper.removeClass('lazy-load').on('transitionend webkitTransitionEnd oTransitionEnd', function () {
        $(this).removeClass('lazy-load-transition');
      });
    }).each(function() {
      if(this.complete) $(this).load();
    });
  });
}

(function () {
  processLazyLoadImages();
  core.bind('loader.loaded', processLazyLoadImages);
  core.bind('afterPopupPlace', processLazyLoadImages);
})(jQuery);
