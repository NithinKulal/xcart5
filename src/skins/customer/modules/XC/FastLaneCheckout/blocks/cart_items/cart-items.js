/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * cart-items.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/blocks/cart_items',
 ['vue/vue',
  'vue/vue.loadable',
  'checkout_fastlane/sections'],
 function(Vue, VueLoadableMixin, Sections) {

  var CartItems = Vue.extend({
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
      cacheSimultaneous: true,
      loader: function() {
        this.$root.$broadcast('reloadingBlock', 3);
        return core.get({
          target: 'checkout',
          widget: 'XLite\\Module\\XC\\FastLaneCheckout\\View\\Blocks\\CartItems'
        }, undefined, undefined, { timeout: 45000 });
      },
      resolve: function() {
        this.parseTotal();
        this.$root.$broadcast('reloadingUnblock', 3);
      },
      reject: function() {
        this.$root.$broadcast('reloadingUnblock', 3);
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

  Vue.registerComponent(Sections, CartItems);

  return CartItems;
});
