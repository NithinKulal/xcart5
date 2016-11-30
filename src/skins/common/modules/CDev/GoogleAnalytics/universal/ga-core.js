/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Core google analytics
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/core', [], function() {
  GACore = Object.extend({

    settings: {
      isDebug:        false,
      addTrace:       false,
      account:        '',
      trackingType:   1,
      sendPageview:   true,
      currency:       'USD',
    },

    constructor: function() {
      if (jQuery('#ga-init-script').length) {
        this.settings = _.defaults(
          jQuery('#ga-init-script').data('settings'),
          this.settings
        );
      }

      if (this.settings.isDebug && this.settings.addTrace) {
        window.ga_debug = {
          trace: this.settings.addTrace
        };
      }

      this.initialize();
    },

    getSetting: function(name) {
      return this.settings[name];
    },

    initialize: function() {
      this.createTracker();

      if (this.settings.sendPageview) {
        jQuery().ready(_.bind(this.sendPageView, this));
      }
    },

    createTracker: function() {
      var options = {};

      if (this.settings.trackingType === 2) {
        options = {
          cookieDomain: '.' + window.location.host.replace(/^[^.]+./, '')
        };
      } else if (this.settings.trackingType === 3) {
        options = {
          'allowLinker': true
        };
      }

      ga('create', this.settings.account, 'auto', options);
    },

    sendPageView: function() {
      core.trigger('ga-pageview-sending');

      ga('send', 'pageview');

      core.trigger('ga-pageview-sent');
    },

    /**
     * Registers the event
     *
     * @param {string} category Typically the object that was interacted with (e.g. button)
     * @param {string} action   The type of interaction (e.g. click)
     * @param {string} label    Useful for categorizing events (e.g. nav buttons)
     * @param {number} value    Values must be non-negative. Useful to pass counts (e.g. 4 times)
     * @return void
     */
    registerEvent: function (category, action, label, value) {
      if ('undefined' != typeof(window._gaq)) {
        _gaq.push(['_trackEvent', category, action, label, value]);

      } else if ('undefined' != typeof(window.ga)) {
        ga('send', 'event', category, action, label, value);
      }
    },
  });

  GACore.instance = new GACore();

  return GACore;
});
