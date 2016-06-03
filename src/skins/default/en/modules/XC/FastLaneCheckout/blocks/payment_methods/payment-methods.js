/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * payment-methods.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.PaymentMethods', [], function() {

  Checkout.PaymentMethods = Vue.extend({
    mixins: [VueLoadableMixin],
    name: 'payment-methods',
    replace: false,

    loadable: {
      transferState: false,
      loader: function() {
        this.$set('payment', {});
        return core.get({
          target: 'checkout',
          widget: 'XLite\\Module\\XC\\FastLaneCheckout\\View\\Blocks\\PaymentMethods'
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
      core.trigger('checkout.paymentTpl.loaded');
    },

    data: function() {
      return {
        methodId: null,
        payment: {}
      };
    },

    computed: {
      paymentData: function() {
        return _.reduce(
          this.payment,
          function(result, value, key) {
            var newKey = 'payment[' + key + ']';
            result[newKey] = value;
            return result;
          }, {}
        );
      },

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
      },
      paymentData: function(value, oldValue) {
        this.triggerUpdate({
          silent: true
        });
      }
    },

    events: {
      global_updatecart: function(data) {
        var triggerKeys = ['paymentMethodsHash', 'paymentMethodId'];
        var needsUpdate = _.some(triggerKeys, function(key) {
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

    methods: {
      triggerUpdate: function(options) {
        options = options || {};
        var eventArgs = _.extend({
          sender: this,
          isValid: this.isValid,
          fields: this.toDataObject()
        }, options);

        this.$dispatch('update', eventArgs);
      },
      toDataObject: function() {
        return {
          paymentData: this.paymentData,
          methodId: this.methodId
        };
      },
    }
  });

});
