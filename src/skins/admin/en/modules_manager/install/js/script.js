/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common rountines
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(
function () {
  var module = _.keys(hash.get())[0];
  if (module) {
    jQuery('.module-' + module).addClass('active');
    window.setTimeout(function () {
      window.scrollBy(0, -150);
      jQuery('.module-' + module).addClass('non-active');
    }, 500);
  }
});
