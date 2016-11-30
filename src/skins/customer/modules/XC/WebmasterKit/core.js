/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Core
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function() {
  var tmp = core.trigger;

  core.trigger = function(name, params)
  {
    var message = this.isReady
        ? 'Fire \'' + name + '\' mediator\'s event' 
        : 'Postponed \'' + name + '\' mediator\'s event until ready';

    if ('undefined' != typeof(console.groupCollapsed) && (params || 'undefined' != typeof(console.trace))) {
      console.groupCollapsed(message);
      if (params) {
        console.log(params);
      }
      if ('undefined' != typeof(console.trace)) {
        console.trace();
      }
      console.groupEnd();

    } else {
      console.log(message);
    }

    return tmp.apply(this, arguments);
  }
})();
