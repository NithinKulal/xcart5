/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Order statuses selector controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(
  function() {
    // Open warning popup
    core.microhandlers.add(
      'openWarningPopupByLink',
      'a.popup-warning',
      function() {
        if (0 < jQuery(this).next('div.status-warning-content').length) {
          attachTooltip(this, jQuery(this).next('div.status-warning-content').html());
        }
      }
    );
  }
);
