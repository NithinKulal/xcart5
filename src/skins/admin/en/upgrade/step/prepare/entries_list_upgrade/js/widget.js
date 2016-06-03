/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Incompatible entries list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
function toggleModulesList() {
  var list = jQuery('.update-module-list.upgrade');

  if (list.is(':visible')) {
    list.hide();
    jQuery('.toggle-list a').text(core.t('show list'))
  } else {
    list.show();
    jQuery('.toggle-list a').text(core.t('hide list'))
  }
}
