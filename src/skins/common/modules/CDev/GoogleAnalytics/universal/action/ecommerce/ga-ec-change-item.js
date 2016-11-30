/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommerceChangeItemEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'],
    function (eCommerceCoreEvent, _) {

      eCommerceChangeItemEvent = eCommerceCoreEvent.extend({

        getListeners: function () {
          return {
            'updateCart':       this.registerItemsChange,
            'ec-item-change':   this.registerItemsChangeExternal
          };
        },

        processReady: function () {
          var self = this;

          _.each(
              this.getActions('items-changed'),
              function (action, index) {
                self.registerItemChangedByAdmin(action.data);
              }
          );

          var orderChangedData = _.first(
              this.getActions('order-changed')
          );

          if (orderChangedData) {
            this.registerOrderChangedByAdmin(
                orderChangedData['data']
            );
          }
        },

        registerItemsChange: function (event, data) {
          if (data.items) {
            for (var i = 0; i < data.items.length; i++) {
              var item = data.items[i];

              item['ga-data']['quantity'] = Math.abs(item.quantity_change);

              this.registerItemChange(
                  item['ga-data'],
                  this.getEventNameByItem(
                      item.quantity_change,
                      item.quantity
                  )
              );
            }
          }
        },

        registerOrderChangedByAdmin: function (data) {
          data.actionData = data.actionData || {};

          ga('ec:setAction', data.actionName, data.actionData);
          ga('send', 'event', 'AOM', data.actionName);
        },

        registerItemChange: function (productData, action) {
          ga('ec:addProduct', productData);
          ga('ec:setAction', action);
          ga('send', 'event', 'Cart', action, action + ' to cart');
        },

        registerItemChangedByAdmin: function (data) {
          var message = 'Item change';
          data.actionData = data.actionData || {};
          if (data.actionName === 'purchase') {
            message = 'Add to cart'
          } else if (data.actionName === 'refund') {
            message = 'Remove from cart'
          }

          ga('ec:addProduct', data.productData);
          ga('ec:setAction', data.actionName, data.actionData);
          ga('send', 'event', 'AOM', data.actionName, message);
        },

        registerItemsChangeExternal: function(event, data) {
          this.registerItemsChange(event, { items: data });
        },

        getEventNameByItem: function(change, qty) {
          if (change > 0) {
            eventName = 'add';
          } else if (change < 0) {
            eventName = 'remove';
          }

          return eventName;
        },

      });

      eCommerceChangeItemEvent.instance = new eCommerceChangeItemEvent();

      return eCommerceChangeItemEvent;
    }
);