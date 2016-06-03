/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Left sidebar box snippet
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(
  function() {
    if (jQuery('#leftSideBar').length) {
      jQuery('#content-header').css('min-height', jQuery('#leftSideBar').outerHeight() + 'px');
    }
  }
);

