/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attributes
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('#useclasses').change(
      function () {
        if ('D' == jQuery(this).val()) {
            jQuery('li.select-classes').show();

        } else {
            jQuery('li.select-classes').hide();
        }
      }
    );

    if ('D' != jQuery('#useclasses').val()) {
        jQuery('li.select-classes').hide();
    }
  }
);
