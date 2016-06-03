/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Banner rotation: customer zone controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function isValidImageUrl(url, callback) {
    var image = new Image();
    image.onerror = function() { callback(false, image); }
    image.onload =  function() { callback(true, image); }
    image.src = url;
}

core.microhandlers.add(
  'barcodeLazyLoader',
  '.barcode-block-wrapper',
  function (event) {
    var wrapper = jQuery(this);
    var imgSrc = wrapper.data('img-src');
    var imgAlt = wrapper.data('img-alt');

    assignWaitOverlay(wrapper);

    isValidImageUrl(
      imgSrc,
      function(isValid, image) {
        if (isValid) {
          image.alt = imgAlt;
          wrapper.append(image);
        } else {
          wrapper.find('.barcode-image-placeholder').removeClass('hidden');
        };
        unassignWaitOverlay(wrapper);
      }
    );
  }
);
