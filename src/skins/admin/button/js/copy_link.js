/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Copy link button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    var clipboard = new Clipboard('button.copy-link');
    clipboard.on('success', function(e) {
      core.trigger('message', {type: 'info', message: core.t('The link was copied to your clipboard')});
    });
  }
);
