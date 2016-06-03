/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * shipping-methods.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.ShippingMethods', [], function() {

  Checkout.ShippingMethods = Vue.extend({
    mixins: [VueLoadableMixin],
    name: 'shipping-methods',
    replace: false,

    loadable: {
      loadOnCompile: $('#shipping-methods-list').data('deferred'),
      transferState: false,
      loader: function() {
        return core.get({
          target: 'checkout',
          widget: 'XLite\\Module\\XC\\FastLaneCheckout\\View\\Blocks\\ShippingMethods'
        });
      },
      resolve: function() {
        this.$root.$broadcast('reloadingUnblock', this);
      },
      reject: function() {
        this.$root.$broadcast('reloadingUnblock', this);
      }
    },

    ready: function() {
    },

    data: function() {
      return {
        selector: null,
        methodId: null,
      };
    },

    computed: {
      classes: function () {
        return {
          'reloading': this.$reloading
        }
      },

      isValid: {
        cache: false,
        get: function() {
          return this.methodId !== null;
        }
      }
    },

    watch: {
      methodId: function(value, oldValue){
        if (oldValue !== null) {
          this.$reloading = true;
          this.$root.$broadcast('reloadingBlock', this);
        }
        this.triggerUpdate({
          silent: oldValue === null
        });
      }
    },

    events: {
      global_updatecart: function(data) {
        // var shippingKeys = ['shippingTotal', 'shippingMethodsHash', 'shippingMethodId'];
        var shippingKeys = ['shippingMethodsHash', 'shippingMethodId'];
        var needsUpdate = _.some(shippingKeys, function(key) {
          return _.has(data, key);
        });

        if (needsUpdate) {
          this.$reload();
        }
      },
      checkout_anyPersist: function(data) {
        this.$reloading = false;
        this.$root.$broadcast('reloadingUnblock', this);
      },
    },

    vuex: {
      actions: {
        updateMethod: function(state, value) {
          state.dispatch('UPDATE_SHIPPING_METHOD', value);
        },
      }
    },

    methods: {
      triggerUpdate: function(options) {
        options = options || {};
        var eventArgs = _.extend({
          sender: this,
          isValid: this.isValid,
          fields: this.toDataObject()
        }, options);

        this.$dispatch('update', eventArgs);
        this.updateMethod(this.methodId);
      },
      toDataObject: function() {
        return {
          methodId: this.methodId
        };
      },
    }
  });

});
