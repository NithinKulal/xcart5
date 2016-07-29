/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Incompatible entries list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind(
  "load",
  function () {
    attachTooltip("td.status-incompatible", jQuery(".incompatible-status-message").html());
  }
);
