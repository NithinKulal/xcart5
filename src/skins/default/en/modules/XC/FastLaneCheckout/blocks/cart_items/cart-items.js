/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * cart-items.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.CartItems', [], function() {

  Checkout.CartItems = Vue.extend({
    mixins: [VueLoadableMixin],
    name: 'cart-items',
    replace: false,

    vuex: {
      actions: {
        updateTotal: function(state, number, text) {
          state.dispatch('UPDATE_TOTAL', number, text);
        },
      }
    },

    loadable: {
      transferState: false,
      loader: function() {
        if (_.has(Checkout.loadableCache, this.$options.name)) {
          return Checkout.loadableCache[this.$options.name];
        } else {
          Checkout.loadableCache[this.$options.name] = core.get({
            target: 'checkout',
            widget: 'XLite\\Module\\XC\\FastLaneCheckout\\View\\Blocks\\CartItems'
          });

          return Checkout.loadableCache[this.$options.name];
        }
      },
      resolve: function() {
        this.parseTotal();
        delete Checkout.loadableCache[this.$options.name];
      },
      reject: function() {
        delete Checkout.loadableCache[this.$options.name];
      }
    },

    ready: function() {
      this.parseTotal();
      core.trigger('checkout.cart_items.ready');
    },

    data: function() {
      return {
        itemsVisible: false,
      };
    },

    computed: {
      classes: function () {
        return {
          'reloading': this.$reloading
        }
      },
      itemsList: function() {
        return {
          display: this.itemsVisible ? 'block' : 'none',
        }
      }
    },

    methods: {
      toggleItems: function() {
        this.itemsVisible = !this.itemsVisible;
      },
      parseTotal: function() {
        var totalsElement = $('.total .surcharge', this.$el);
        var integer = $('.part-integer', totalsElement).text().replace(/[^0-9]/, '');
        var decimal = $('.part-decimal', totalsElement).text().replace(/[^0-9]/, '');
        var number = parseFloat(integer + '.' + decimal);
        var text = totalsElement.text();

        this.updateTotal(number, text);
      }
    },

    events: {
      global_updatecart: function(data) {
        var triggerKeys = ['shippingTotal', 'total'];
        var needsUpdate = _.some(triggerKeys, function(key) {
          return _.has(data, key);
        });

        if (needsUpdate) {
          this.$reload();
        }
      },

    },
  });

});
