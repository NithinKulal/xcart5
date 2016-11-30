/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Drupal-specific controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/searchEvent', [ 'googleAnalytics/event' ], function(Event) {
  GASearchEvent = Event.extend({
    namespace: 'search',

    processReady: function() {
      // Register "Search" event
      var searchTextInput = jQuery(".search-product-form input[name='substring']");

      if (searchTextInput.length) {
        if (searchTextInput.val()) {
          this.registerSearchSubstring(searchTextInput.val());
        }

        jQuery(".search-product-form button[type='submit']").click(_.bind(function (event) {
          this.registerSearchSubstring(searchTextInput.val());
        }, this));
      }
    },

    registerSearchSubstring: function(substring) {
      if (substring) {
        this.sendEvent('do_search', substring);
      }
    }
  });
  GASearchEvent.instance = new GASearchEvent();

  return GASearchEvent;
});

