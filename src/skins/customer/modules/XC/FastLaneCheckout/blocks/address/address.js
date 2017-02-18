/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * address.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/blocks/address',
  ['vue/vue',
   'vue/vue.loadable',
   'checkout_fastlane/sections',
   'checkout_fastlane/sections/shipping',
   'checkout_fastlane/sections/payment',
   'checkout_fastlane/blocks/shipping_details'],
  function(Vue, VueLoadableMixin, Sections, ShippingSection, PaymentSection, ShippingDetails) {

  var Address = Vue.extend({
    mixins: [VueLoadableMixin],
    name: 'address',
    replace: false,

    loadable: {
      transferState: false,
      cacheSimultaneous: true,
      cacheKey: function() {
        return this.$options.name + this.type + this.display;
      },
      loader: function() {
        this.$root.$broadcast('reloadingBlock', 3);
        return core.get({
          target: 'checkout',
          widget: 'XLite\\Module\\XC\\FastLaneCheckout\\View\\Blocks\\Address\\' + this.type.charAt(0).toUpperCase() + this.type.slice(1),
          display: this.display
        }, undefined, undefined, { timeout: 45000 });
      },
      resolve: function() {
        this.$root.$broadcast('reloadingUnblock', 3);
      },
      reject: function() {
        this.$root.$broadcast('reloadingUnblock', 3);
      }
    },

    vuex: {
      getters: {
        vuex_address: function(state) {
          return state.order.address;
        },
        vuex_same_as_shipping: function(state) {
          return state.order.same_address;
        }
      }
    },

    data: function() {
      return {
        type: null,
        addressId: null,
        fieldsDefault: null,
        display: 'full'
      };
    },

    ready: function() {
      core.autoload(PopupButtonAddressModify);
    },

    computed: {
      fields: function() {
        if (!this.type) {
          return {};
        }

        var fields = this.fieldsDefault;

        if (this.address) {
          fields = _.extend(fields, this.address); 
        }

        return this.preprocess(fields);
      },
      address: function () {
        if (!_.isUndefined(this.vuex_address[this.type])) {
          return this.vuex_same_as_shipping
            ? this.vuex_address['shipping']
            : this.vuex_address[this.type];
        }

        return null;
      },
      classes: function () {
        return {
          'reloading': this.$reloading,
        }
      },
      btnTitle: function() {
        return core.t("Edit address");
      }
    },

    events: {
      global_updatecart: function(data) {
        var reloadKey = this.type + 'AddressId';
        var updateKey = this.type + 'AddressFields';

        if (_.has(data, reloadKey)) {
          this.$reload();
        } else if (_.has(data, updateKey)) {
          this.fieldsDefault = _.extend(this.fieldsDefault, data[updateKey]);
        }
      },
    },

    methods: {
      preprocess: function(address) {
        var result = {}
        for(var key in address) {
          var preprocessor = this['preprocess$' + key];
          result[key] = _.isFunction(preprocessor) ? preprocessor(key, address) : address[key];
        }

        return result;
      },
      preprocess$type: function(key, address) {
        var value = address[key];

        if (!_.isUndefined(window.addressTypes)) {
          var name = window.addressTypes[value];

          if (!_.isUndefined(name)) {
            value = name;
          }
        }

        return value;
      },
      preprocess$country_code: function(key, address) {
        var value = address[key];
        if (!_.isUndefined(window.countryNames)) {
          var country_name = _.findWhere(window.countryNames, { key: value });

          if (!_.isUndefined(country_name)) {
            value = country_name.name;
          }
        }

        return value;
      },
      preprocess$state_id: function(key, address) {
        var value = address[key];
        if (!_.isUndefined(window.stateNames)) {
          var state_name = _.findWhere(window.stateNames, { key: parseInt(value) });

          if (!_.isUndefined(state_name)) {
            value = state_name.name;
          }
        }

        var countryCode = address['country_code'];

        if (_.isUndefined(window.statesList[countryCode])) {
          value = '';
        }

        return value;
      },
      preprocess$custom_state: function(key, address) {
        var value = address[key];

        var countryCode = address['country_code'];

        if (!_.isUndefined(window.statesList[countryCode])) {
          value = '';
        }

        return value;
      }
    }
  });

  Vue.registerComponent(Sections, Address);
  Vue.registerComponent(ShippingSection, Address);
  Vue.registerComponent(PaymentSection, Address);
  Vue.registerComponent(ShippingDetails, Address);

  return Address;
});
