/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * shipping-methods.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/blocks/shipping_methods', 
 ['vue/vue',
  'vue/vue.loadable',
  'checkout_fastlane/sections/shipping'],
  function(Vue, VueLoadableMixin, ShippingSection) {

  var ShippingMethods = Vue.extend({
    mixins: [VueLoadableMixin],
    name: 'shipping-methods',
    replace: false,

    loadable: {
      loadOnCompile: $('#shipping-methods-list').data('deferred'),
      transferState: false,
      loader: function() {
        this.$root.$broadcast('reloadingBlock', 1);
        return core.get({
          target: 'checkout',
          widget: 'XLite\\Module\\XC\\FastLaneCheckout\\View\\Blocks\\ShippingMethods'
        }, undefined, undefined, { timeout: 45000 });
      },
      resolve: function() {
        // updates window.shippingMethodsList
        $.globalEval($('shipping-methods script[type="application/javascript"]').text());
        this.$root.$broadcast('reloadingUnblock', 1);
      },
      reject: function() {
        this.$root.$broadcast('reloadingUnblock', 1);
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
          this.$root.$broadcast('reloadingBlock', 1);
        }
        this.triggerUpdate({
          silent: oldValue === null
        });
      }
    },

    events: {
      sectionPersist: function(data) {
        this.$root.$broadcast('reloadingUnblock', 1);
      },
      global_createshippingaddress: function(data) {
        this.$reload();
      },
      global_updatecart: function(data) {
        var shippingKeys = ['shippingMethodsHash', 'shippingMethodId'];
        var needsUpdate = _.some(shippingKeys, function(key) {
          return _.has(data, key);
        });

        if (needsUpdate) {
          this.$reload();
        }
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

  Vue.registerComponent(ShippingSection, ShippingMethods);

  return ShippingMethods;
});
