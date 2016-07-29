/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * order-notes.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.OrderNotes', [], function() {

  Checkout.OrderNotes = Vue.extend({
    name: 'order-notes',
    replace: false,

    data: function() {
      return {
        notes: null
      };
    },

    watch: {
      notes: function (value, oldValue) {
        this.triggerUpdate({
          silent: oldValue === null
        });
      },
      isValid: function() {
        return true;
      }
    },

    vuex: {
      actions: {
        updateNotes: function(state, value) {
          state.dispatch('UPDATE_NOTES', value);
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
        this.updateNotes(this.notes);
      },
      toDataObject: function() {
        return {
          notes: this.notes
        };
      },
    }
  });

});
