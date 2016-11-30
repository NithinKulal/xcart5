/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/event', [ 'googleAnalytics/core' ], function(Core) {
  GAEvent = Object.extend({
    constructor: function() {
      jQuery().ready(_.bind(this.processReady, this));

      this.bindListeners();
    },

    bindListeners: function() {
      _.each(
          this.getListeners(),
          _.bind(
            function (handler, eventName) {
              core.bind(eventName, _.bind(handler, this));
            },
            this
          )
      );
    },

    getListeners: function() {
      return {};
    },

    processReady: function() {},

    sendEvent: function(name, label, value, namespace) {
      Core.instance.registerEvent(namespace || this.namespace, name, label, value);
    }
  });

  return GAEvent;
});