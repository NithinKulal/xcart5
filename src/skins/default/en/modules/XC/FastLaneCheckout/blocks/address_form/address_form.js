/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * address_form.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.AddressForm', [], function(){
  Checkout.AddressField = Vue.extend({
    name: 'form-field',
    replace: false,

    data: function() {
      return {
        name: '',
        value: null,
        required: false
      };
    },

    watch: {
      value: function(value, oldValue) {
        this.$dispatch('modify', value, oldValue);
      }
    },

    ready: function() {
      this.name = this.$els.input.getAttribute('name');
      this.required = jQuery(this.$els.input).hasClass('field-required');
      // new CommonElement(this.$els.input);
    },
  });

  Checkout.AddressForm = Vue.extend({
    name: 'address-form',
    replace: false,

    components: {
      FormField: Checkout.AddressField
    },

    ready: function() {
      // Legacy form validation
      new CommonForm(this.form);
      core.trigger('checkout.address_form.ready');
    },

    data: function() {
      return {
        visible: true,
        userInputed: false,
        fieldsWereFilled: false,
      };
    },

    computed: {
      form: function() {
        return this.$el.querySelectorAll('form')[0];
      },

      classes: function () {
        return {
          'hidden': !this.visible
        }
      },

      fieldsWereFilledOnce: {
        cache: false,
        get: function () {
          var isAllFilled = this.$children.every(function(item){
            return !item.required || !_.isEmpty(item.value);
          });

          var result = isAllFilled || this.fieldsWereFilled;

          this.fieldsWereFilled = result;

          return result;
        }
      },

      isValid: {
        cache: false,
        get: function() {
          return !_.isUndefined(this.form.commonController) && this.form.commonController.validate({
            silent: !this.userInputed || !this.fieldsWereFilledOnce,
            focus: false
          });
        }
      }
    },

    events: {
      modify: function(value, oldValue) {
        this.userInputed = true;

        this.triggerUpdate({
          silent: oldValue === null
        });
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
      },

      toggle: function(state) {
        this.visible = state;
      },

      toDataObject: function() {
        return this.$children.reduce(
          function(acc, item){
            acc[item.name] = item.value;
            return acc;
          },
          {}
        );
      },
    }
  });
});