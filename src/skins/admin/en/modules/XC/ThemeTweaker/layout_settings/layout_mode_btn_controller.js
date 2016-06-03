/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function LayoutModeSwitcher() {
  var o = this;
  jQuery('#layout-mode').change(function (event)
  {
    event.stopImmediatePropagation();

    var switchWrapper = jQuery('.layout-mode-switch');
    if (switchWrapper.length) {
      assignShadeOverlay(switchWrapper);
    }

    core.get(
      URLHandler.buildURL({
        target: 'layout',
        action: 'switch_layout_mode'
      }),
      function () {
        if (switchWrapper) {
          unassignShadeOverlay(switchWrapper);
        }
      }
    );

    jQuery('.layout-mode-comment a').toggleClass('hidden', !jQuery(this).is(':checked'));

    return false;
  });
}

core.autoload(LayoutModeSwitcher);