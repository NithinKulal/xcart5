/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add shipping method controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'addShippingTab',
  '.add-shipping .page-tabs .tab a',
  function () {
    var setDialogPosition = _.bind(function () {
      var dialog = jQuery(this).closest('.ui-dialog-content');
      setTimeout(function () {
        dialog.dialog('option', 'position', {my: 'center center', at: 'center center'});
      }, 0);
    }, this);

    jQuery(this).bind('click', setDialogPosition);
    setDialogPosition();
  }
);

core.microhandlers.add(
  'offlineHelp',
  '.offline-help .help-link',
  function () {
    jQuery(this).bind('click', function () {
      jQuery('.offline-help .help-content').toggle();
    });
  }
);
