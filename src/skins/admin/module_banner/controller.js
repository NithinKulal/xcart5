/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Module banner controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(function () {
  jQuery('.module-banner .close-banner').bind('click', function () {
    jQuery.ajax({
      url: xliteConfig.script + "?target=main&action=close_module_banner&module=" + jQuery(this).data('module-name')
    }).done(function() {
    });

    jQuery(this).closest('.module-banner').hide();
  })
});