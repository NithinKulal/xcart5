/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * sections.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.StoreSections', [], function(){
	Checkout.StoreSections = {
    state: {
      current: null,
      enabled: [],
      list: {}
    },

    mutations: {
      REGISTER_SECTION: function (state, name, component) {
        Vue.set(state.list, name, {});
        Vue.set(state.list[name], 'fields',     {});
        Vue.set(state.list[name], 'complete',   false);
        Vue.set(state.list[name], 'name',       name);
        Vue.set(state.list[name], 'index',      component.index);
        Vue.set(state.list[name], 'nextLabel',  component.nextLabel);

        if (null === state.current
          && name === 'address' // Hack for registering address sections first
        ) {
          state.enabled.push(name);
          state.current = state.list[name];
        }
      },

      SWITCH_SECTION: function (state, name) {
        state.current = state.list[name];
      },

      TOGGLE_SECTION: function (state, name, value) {
        if (value) {
          state.enabled.push(name);
        } else {
          state.enabled.$remove(name);
        }
      },

      TOGGLE_COMPLETE: function (state, name, value) {
        state.list[name].complete = value;
      },

      UPDATE_SECTION_FIELDS: function (state, name, data) {
        state.list[name].fields = _.extend(state.list[name].fields, data);
      },
    }
	}
});
