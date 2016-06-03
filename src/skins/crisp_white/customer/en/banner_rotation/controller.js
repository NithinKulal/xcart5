/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Banner rotation: customer zone controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'BannerRotation',
  '#banner-rotation-widget',
  function (event) {
    var $this = jQuery(this);
    var options = core.getCommentedData(this);
    $this.carousel(options);
    $this.carousel('cycle');

    var firstItem = $this.find('.item').first();
    firstItem.addClass('active');

    var firstIndicator = $this.find('.carousel-indicators li').first();
    firstIndicator.addClass('active');

    /*
    var maxHeight = firstItem.find('img').height();
    if (maxHeight > 0) {
      firstItem.find('img').onload = function () {
        jQuery('#banner-rotation-widget .carousel-inner').height(Math.floor(maxHeight / 2));
      };

      jQuery('#banner-rotation-widget .carousel-inner').height(Math.floor(maxHeight / 2));
    }
    */
  }
);
