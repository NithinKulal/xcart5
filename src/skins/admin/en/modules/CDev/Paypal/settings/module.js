/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * PayPal model page controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('.general-settings .model-properties ul.table li.select-protocol div.table-value .toggle-edit').click(
      function() {
        jQuery('.general-settings .model-properties ul.table li.select-protocol div.table-value .view').toggle();
        jQuery('.general-settings .model-properties ul.table li.select-protocol div.table-value .value').toggle();
      }
    );
  }
);
