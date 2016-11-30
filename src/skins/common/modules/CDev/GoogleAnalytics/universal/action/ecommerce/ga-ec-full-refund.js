/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommerceFullRefundEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'],
    function (eCommerceCoreEvent, _) {

      eCommerceFullRefundEvent = eCommerceCoreEvent.extend({

        getListeners: function () {
          return {
            'ec-full-refund':   this.registerExternal
          };
        },

        processReady: function () {
          var self = this;

          _.each(
              this.getActions('full-refund'),
              function (action, index) {
                self.registerFullRefund(action.data);
              }
          );
        },

        registerFullRefund: function (data) {
          data.actionData = data.actionData || {};

          ga('ec:setAction', data.actionName, data.actionData);
          ga('send', 'event', 'AOM', 'Changed to not paid status');
        },

        registerExternal: function(event, data) {
          this.registerFullRefund(data);
        },

      });

      eCommerceFullRefundEvent.instance = new eCommerceFullRefundEvent();

      return eCommerceFullRefundEvent;
    }
);