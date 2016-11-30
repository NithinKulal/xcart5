/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Incompatible entries list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function RequestForUpgrade () {
  this.button = jQuery('button.request-for-upgrade');

  var self = this;
  this.button.click(function () {
    self.sendRequest();
  })
}

RequestForUpgrade.prototype.sendRequest = function () {
  core.post(
    {
      target: 'upgrade',
      action: 'request_for_upgrade'
    },
    _.bind(this.success, this)
  );
};

RequestForUpgrade.prototype.success = function () {
  this.button.get(0).progressState.setStateSuccess();
};

core.autoload(RequestForUpgrade);
