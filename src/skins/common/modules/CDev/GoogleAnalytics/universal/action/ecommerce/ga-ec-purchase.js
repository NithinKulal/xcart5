/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommercePurchaseEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'],
    function (eCommerceCoreEvent, _) {

      eCommercePurchaseEvent = eCommerceCoreEvent.extend({

        getListeners: function () {
          return {
            'load':                 this.registerFullPurchase,
            'ga-pageview-sending':  this.registerPurchase,
            'ga-ec-purchase':       this.registerPurchaseExternal
          };
        },

        registerFullPurchase: function (event, data) {
          var actionData = _.first(this.getActions('admin-purchase'));

          if (actionData) {
            this._registerPurchase(
                actionData['data']['products'],
                actionData['data']['actionData']
            );

            ga('send', 'event', 'AOM', 'Changed to paid status');
          }
        },

        registerPurchase: function (event, data) {
          var actionData = _.first(this.getActions('purchase'));

          if (actionData) {
            this._registerPurchase(
                actionData['data']['products'],
                actionData['data']['actionData']
            );
          }
        },

        registerPurchaseExternal: function(event, data) {
          if (data) {
            this._registerPurchase(
                data['products'],
                data['actionData']
            );
          }

          ga('send', 'event', 'Ecommerce', 'Checkout successful');
        },

        _registerPurchase: function (productsData, actionData) {
          _.each(productsData, function(product) {
            ga('ec:addProduct', product);
          });

          ga('ec:setAction', 'purchase', actionData);
        },

      });

      eCommercePurchaseEvent.instance = new eCommercePurchaseEvent();

      return eCommercePurchaseEvent;
    }
);