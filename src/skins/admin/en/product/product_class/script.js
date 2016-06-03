/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product class
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('div.product-class-text button').click(
      function () {
        jQuery('div.product-class-text').hide();
        jQuery('div.product-class-select').attr('style', 'display:table');
        return false;
      }
    );

    jQuery('div.product-class-select select').change(
      function () {
        if (jQuery(this).val() == -1) {
            jQuery('div.product-class-select input').show();
        } else {
            jQuery('div.product-class-select input').hide();
        }
      }
    );
  }
);
