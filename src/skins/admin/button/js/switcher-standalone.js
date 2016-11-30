/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ButtonSwitcherStandalone(base) {
  this.base = base;
  var self = this;

  jQuery(this.base).change(
      function (event) {
        event.stopImmediatePropagation();
        
        self.executeShaded(function(onSuccess) {
          self.runAction(onSuccess);
        });
      }
  );
}

ButtonSwitcherStandalone.prototype.executeShaded = function (callback) {
  event.stopImmediatePropagation();

  var switchWrapper = jQuery(this.base);
  
  assignShadeOverlay(switchWrapper);

  callback(function() {
    unassignShadeOverlay(switchWrapper);
  });

  return false;
};

ButtonSwitcherStandalone.prototype.runAction = function (onSuccess) {
  var data = core.getCommentedData(this.base);

  core.get(data.url, onSuccess, data.data);

  return false;
};

core.microhandlers.add(
    'standalone-switch',
    '.standalone-switch',
    function (event) {
      new ButtonSwitcherStandalone(this);
    }
);