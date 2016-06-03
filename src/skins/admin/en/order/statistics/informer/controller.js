/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */

jQuery().ready(
  function() {

    jQuery('.order-statistics-mini-informer .header').live('click', function () {
      jQuery(this).parent().toggleClass('collapsed');
      jQuery(this).parent().toggleClass('expanded');
    });

  }
);
