/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommerceDetailsShownEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'],
    function (eCommerceCoreEvent, _) {

      eCommerceDetailsShownEvent = eCommerceCoreEvent.extend({

        getListeners: function () {
          return {
            'ga-pageview-sending': this.registerAllDetailsShown,
            'ga-ec-details-shown': this.registerAllDetailsShownExternal,
          };
        },

        registerAllDetailsShown: function (event, data) {
          var self = this;

          _.each(
              this.getActions('addProduct'),
              function (action, index) {
                self.registerDetailsShown(action.data);
              }
          );
        },

        registerDetailsShown: function (addProductData) {
          ga('ec:addProduct', addProductData);

          ga('ec:setAction', 'detail');
        },

        registerAllDetailsShownExternal: function (event, data) {
          this.registerDetailsShown(data.data);

          var list = _.isUndefined(data.data.list)
              ? ''
              : { list: data.data.list };

          message =  _.isUndefined(data.message)
              ? 'Details shown'
              : data.message;

          ga('send', 'event', 'UX', message, list);
        },

      });

      eCommerceDetailsShownEvent.instance = new eCommerceDetailsShownEvent();

      return eCommerceDetailsShownEvent;
    }
);