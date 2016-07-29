/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * create backup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function EmailLinksButton () {
  this.button = jQuery('button.email-links');

  var self = this;
  this.button.click(function () {
    self.sendRequest();
  })
}

EmailLinksButton.prototype.sendRequest = function () {
  var params = {
      target: 'safe_mode',
      action: 'email_links',
  };
  params[xliteConfig.form_id_name] = xliteConfig.form_id;
  core.post(
    {
      target: 'safe_mode',
      action: 'email_links',
    },
    _.bind(this.success, this),
    params
  );
};

EmailLinksButton.prototype.success = function () {
  this.button.get(0).progressState.clearState();
  this.button.get(0).progressState.setStateStill();
  this.button.get(0).progressState.setLabel('Email again');
};

core.autoload(EmailLinksButton);
