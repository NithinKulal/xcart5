/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Additional product page controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('.usecustomog-value .control select').change(
      function () {
        jQuery('.usecustomog-value .og-textarea').toggle(jQuery(this).val() == 1);
      }
    );

    jQuery('.usecustomog-value .control select').change();
  }
);
