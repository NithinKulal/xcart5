/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function WebmaserSwitcher () {
  var o = this;
  jQuery('#edit-mode').change(function (event)
  {
    event.stopImmediatePropagation();

    var switchWrapper = jQuery('.webmaster-mode-switch');
    if (switchWrapper.length) {
      assignShadeOverlay(switchWrapper);
    }

    core.get(
      URLHandler.buildURL({
        target: 'theme_tweaker_templates',
        action: 'switch'
      }),
      function () {
        if (switchWrapper) {
          unassignShadeOverlay(switchWrapper);
        }
      }
    );

    jQuery('.edit-mode-comment a').toggleClass('hidden', !jQuery(this).is(':checked'));

    return false;
  });
}

core.autoload(WebmaserSwitcher);