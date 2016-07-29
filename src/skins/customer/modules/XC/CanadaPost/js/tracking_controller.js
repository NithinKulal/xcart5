/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Canda Post tracking details controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('a.capost-tracking-link').click(
      function () {
        return !popup.load(
          this,
          null,
          false
        );
      }
    );
  }
);
