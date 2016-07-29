/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * address.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.Address', [], function() {

  Checkout.Address = Vue.extend({
    name: 'address',
    replace: false,

    vuex: {
      getters: {
        same_address_flag: function(state) {
          return state.order.same_address;
        },
        address: function(state) {
          return state.order.address;
        }
      }
    },

    data: function() {
      return {
        type: null
      };
    },

    computed: {
      fields: function() {
        if (!this.type) {
          return {};
        }

        if (!this.address[this.type]) {
          return {};
        }

        return this.preprocess(this.address[this.type]);
      },
      title: function() {
        return core.t(this.type.charAt(0).toUpperCase() + this.type.slice(1) + ' to');
      },
      person: function() {
        if (this.same_address) {
          return core.t('same as shipping');
        } else {
          return '';
        }
      },
      same_address: function() {
        return this.same_address_flag === 1 && this.type === 'billing';
      },
    },

    events: {
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

        return value;
      },
    },
  });

});
