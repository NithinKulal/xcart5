/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Dropdown button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('div.btn-dropdown button.trigger-first-item').click(
      function () {
        jQuery(this).parent().find('li button:first').click();
        return false;
      }
    );
  }
);
