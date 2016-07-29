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
    if ('undefined' != typeof(console.groupCollapsed) && (params || 'undefined' != typeof(console.trace))) {
      console.groupCollapsed('Fire \'' + name + '\' mediator\'s event');
      if (params) {
        console.log(params);
      }
      if ('undefined' != typeof(console.trace)) {
        console.trace();
      }
      console.groupEnd();

    } else {
      console.log('Fire \'' + name + '\' mediator\'s event');
    }

    return tmp.apply(this, arguments);
  }
})();

