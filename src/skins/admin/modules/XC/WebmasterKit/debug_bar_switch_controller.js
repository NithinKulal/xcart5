/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function DebugBarSwitcher() {
  var o = this;
  jQuery('#debugbarenabled').change(function (event)
  {
    event.stopImmediatePropagation();

    var switchWrapper = jQuery('.debug-bar-switch');
    if (switchWrapper.length) {
      assignShadeOverlay(switchWrapper);
    }

    core.get(
      URLHandler.buildURL({
        target: 'layout',
        action: 'switch_debug_bar'
      }),
      function () {
        if (switchWrapper) {
          unassignShadeOverlay(switchWrapper);
        }
      }
    );

    return false;
  });
}

core.autoload(DebugBarSwitcher);
