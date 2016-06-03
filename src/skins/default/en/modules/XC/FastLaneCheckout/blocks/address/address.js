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
        fieldset: function(state) {
          return state.sections.list.address.fields;
        }
      }
    },

    data: function() {
      return {
        type: null,
        fields: {}
      };
    },

    computed: {
      title: function() {
        return this.type.charAt(0).toUpperCase() + this.type.slice(1);
      },
      person: function() {
        if (_.isEmpty(this.fields) || this.same_address) {
          return core.t('same as shipping');
        } else {
          return '';
        }
      },
      same_address: function() {
        return this.fields.same_address === 1 && this.type === 'billing';
      },
      filterRegex: function() {
        return new RegExp('^' + this.type + 'Address\\[(.*)\\]');
      },
    },

    events: {
      sectionUpdate: function(name) {
        if (name == 'address') {
          var update = _.reduce(this.fieldset, this.filterByType, {}, this);
          this.$set('fields', this.preprocess(update));
        }
      }
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
      filterByType: function(acc, value, key, list) {
        var name = key.match(this.filterRegex);

        if (name !== null) {
          acc[name[1]] = value;
        } else if (key.match(/\[/i) == null) {
          acc[key] = value;
        }

        return acc;
      }
    },

    directives: {
      addressfield: {
        deep: true,
        update: function () {
          return this.vm.fields[this.expression];
        }
      }
    }
  });

});
