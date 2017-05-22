/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommerceCheckoutStepEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'],
    function (eCommerceCoreEvent, _) {

      eCommerceCheckoutStepEvent = eCommerceCoreEvent.extend({

        getListeners: function () {
          return {
            'ga-pageview-sending':    this.registerCheckoutEnter,
            'ga-pageview-sent':       this.registerInitialCheckoutOptions,
            'ga-ec-checkout':         this.registerCheckoutExternal,
            'ga-ec-checkout-option':  this.registerCheckoutOptionExternal,
          };
        },

        registerCheckoutEnter: function (event, data) {
          var actionData = _.first(
              this.getActions('checkout')
          );

          if (actionData) {
            this._registerCheckoutEnter(
                actionData['data']['products'],
                actionData['data']['actionData']
            );
          }
        },

        registerCheckoutExternal: function(event, data) {
          if (!data
            || _.isUndefined(data['products'])
            || _.isUndefined(data['actionData'])
          ) {
            return;
          }

          this._registerCheckoutEnter(
              data['products'],
              data['actionData']
          );
          ga('send', 'event', 'Checkout', data.message || 'Checkout entered');
        },

        registerInitialCheckoutOptions: function(event, data) {
          var self = this;

          _.each(
              this.getActions('checkout-option'),
              function (action, index) {
                self._registerCheckoutOption(action.data);
                ga('send', 'event', 'Checkout', 'Option');
              }
          );
        },

        registerCheckoutOptionExternal: function(event, data) {
          this._registerCheckoutOption(data);

          if (!_.isUndefined(ga.loaded) && ga.loaded) {
            ga('send', 'event', 'Checkout', 'Option', {
              hitCallback: function() {
                core.trigger('ga-option-sent', data);
              }
            });
          } else {
            core.trigger('ga-option-sent', data);
          }
        },

        _registerCheckoutOption: function(data) {
          if (!data || _.isUndefined(data.option)) {
            return;
          }

          ga('ec:setAction', 'checkout_option', data);
        },

        _registerCheckoutEnter: function (productsData, actionData) {
          _.each(productsData, function(product) {
            ga('ec:addProduct', product);
          });
          ga('ec:setAction', 'checkout', actionData || {});
        },

      });

      eCommerceCheckoutStepEvent.instance = new eCommerceCheckoutStepEvent();

      return eCommerceCheckoutStepEvent;
    }
);
