/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Popup-singleton
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(function(){
  jQuery('.recently-viewed-products .product-cell > .product').each(function(index, elem) {
    var content = jQuery(elem).find('.recently-viewed-product-details').html();

    jQuery(elem).popover({
      content:    content,
      container:  'body',
      placement:  'auto top',
      trigger:    'hover',
      html:       true,
    });
  });
});
