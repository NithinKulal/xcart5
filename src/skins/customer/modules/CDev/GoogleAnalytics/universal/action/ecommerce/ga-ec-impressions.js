/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommerceCoreEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'], function (eCommerceCoreEvent, _) {
  eCommerceImpressionEvent = eCommerceCoreEvent.extend({
    processsedImpressions: [],

    getListeners: function () {
      return {
        'ga-pageview-sending':      this.registerAllImpressionsInitial,
        'list.products.loaded':     this.registerAllImpressionsInList,
        'ga-ec-addImpression':      this.addImpressionExternalHandler,
      };
    },

    registerAllImpressionsInitial: function (event, data) {
      var self = this;

      _.each(
          this.getActions('impression'),
          function (action, index) {
            self.addImpression(action.data);
          }
      );
    },

    registerAllImpressionsInList: function (event, widget) {
      var self = this;

      var shouldSend = false;

      _.each(
          this.getActions('impression', widget['base']),
          function (action, index) {
            shouldSend = self.addImpression(action.data);
          }
      );

      if (shouldSend) {
        ga('send', 'event', 'Ecommerce', 'Dynamic list impressions');
      }
    },

    addImpression: function (impressionData) {
      var result = false;
      var hash = window.core.utils.hash(
          _.omit(impressionData, 'position')
      );

      if (!this.processsedImpressions[hash]) {
        ga('ec:addImpression', impressionData);
        result = true;

        this.processsedImpressions[hash] = true;
      }

      return result;
    },

    addImpressionExternalHandler: function (event, data) {
      this.addImpression(data);
    },

  });

  eCommerceImpressionEvent.instance = new eCommerceImpressionEvent();

  return eCommerceImpressionEvent;
});