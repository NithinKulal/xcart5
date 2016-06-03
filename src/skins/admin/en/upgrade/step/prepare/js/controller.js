/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * JS-controller for Prepare update step
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ReValidateKeys () {
  this.button = jQuery('.revalidate-keys button');

  var self = this;
  this.button.click(function () {
    self.sendRequest();
  })
}

ReValidateKeys.prototype.sendRequest = function () {
  core.post(
    {
      target: 'upgrade',
      action: 'validate_keys'
    },
    _.bind(this.success, this)
  );
};

ReValidateKeys.prototype.success = function () {
  this.button.get(0).progressState.setStateSuccess();
  location.reload();
};

core.autoload(ReValidateKeys)
