/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * address_form.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.AddressForm', [], function(){

  Checkout.AddressForm = Vue.extend({
    name: 'address-form',
    replace: false,

    created: function() {
      this.nonPersistMode = false;
    },

    ready: function() {
      // Legacy form validation
      new CommonForm(this.form);
      core.autoload(PopupButtonAddressBook);
      this.triggerUpdate({
        silent: true
      });

      core.trigger('checkout.address_form.ready');
    },

    data: function() {
      return {
        fields: window.WidgetData[this.$options.name],
        loginExists: null,
        create_profile: null,
        visible: true,
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

      countryHasStates: function() {
        return typeof window['statesList'] != "undefined"
              && window['statesList'].hasOwnProperty(this.fields.country_code.toUpperCase());
      },

      isStateValid: function() {
        return !!(this.fields.state_id || !this.countryHasStates);
      },

      isValid: {
        cache: false,
        get: function() {
          return this.isStateValid && !_.isUndefined(this.form.commonController) && this.form.commonController.validate({
            silent: !this.form.commonController.isChanged(true) || !this.form.commonController.wasFilledOnce(),
            focus: false
          });
        }
      }
    },

    events: {
      requestNextNotReady: function() {
        if ($(this.form).is(':visible') && !_.isUndefined(this.form.commonController)) {
          this.form.commonController.validate({
            silent: false,
            focus: true
          });
        }
      },
      modify_create_profile: function(value, oldValue) {
        this.create_profile = value;

        // hack
        if (oldValue === null) {
          oldValue = 'undefined';
        }

        this.$emit('modify', value, oldValue);
      },

      modify: function(value, oldValue, forcePersist) {
        this.triggerUpdate({
          silent: oldValue === null || this.nonPersistMode,
          force: forcePersist
        });
      },

      global_selectcartaddress: function(data) {
        if (data.type == this.shortType && !_.isEmpty(data.fields)) {
          this.nonPersistMode = true;
          this.fields = _.extend(this.fields, data.fields);

          this.$nextTick(function() {
            jQuery('.field-country_code', this.form).change();
            jQuery('.field-state_id', this.form).val(this.fields.state_id);
          });
        }
      },

      global_loginexists: function(data) {
        this.loginExists = data.value;
      },

      global_invalidelement: function(event) {
        var ctrl = this.form.commonController;
        if (ctrl && ctrl.form.elements.namedItem(event.name)) {
          ctrl.form.elements.namedItem(event.name).markAsInvalid(event.message, null, true)
        }
      }
    },

    watch: {
      'fields': {
        deep: true,
        handler: function(value) {
          this.$emit('modify', value);
        }
      },
      'fields.email': function(value, oldValue) {
        this.$dispatch('trigger_email_check', {
          email: value,
        });
      },
      create_profile: function(value, oldValue) {
        $('.item-password').toggleClass('hidden', !value);
      },
      loginExists: function(value, oldValue) {
        $('.item-email .email-comment').hide();
        $('.item-email .subbox').hide();

        if (value) {
          this.create_profile = false;
          $('.item-email .subbox.create-warning').css("display", "inline-block")
            .find('a.log-in').click(
              function(event) {
                loadDialogByLink(
                  event.currentTarget,
                  URLHandler.buildURL({
                    'target':  'login',
                    'widget':  '\\XLite\\View\\Authorization',
                    'popup':   1,
                    'fromURL': URLHandler.buildURL({'target': 'checkout'}),
                    'login':   $('#email').val() || ''
                  }),
                  {width: 'auto'},
                  null,
                  this
                );

                return false;
              }
            )

        } else {
          $('.item-email .subbox.create').css("display", "inline-block");
        }
      }
    },

    methods: {
      triggerUpdate: function(options) {
        options = options || {};
        var eventArgs = _.extend({
          sender: this,
          isValid: options.silent ? false : this.isValid,
          fields: this.preprocess(this.toDataObject()),
        }, options);

        this.$dispatch('update', eventArgs);

        this.$nextTick(function() {
          this.nonPersistMode = false;
        });
      },

      preprocess: function(data) {
        var result =  _.reduce(data, function(acc, value, index) {
          acc[this.getNameFromInput(index)] = value;
          return acc;
        }, {}, this);

        return result;
      },

      getNameFromInput: function(shortname) {
        if ('undefined' === typeof(this.namesCache)) {
          this.namesCache = {};
        }

        if ('undefined' === typeof(this.namesCache[shortname])) {
          var input = $('[data-shortname=' + shortname + ']', this.form);

          if (input.length > 0) {
            this.namesCache[shortname] = input.attr('name');
          } else {
            this.namesCache[shortname] = shortname;
          }
        }

        return this.namesCache[shortname];
      },

      toggle: function(state) {
        this.visible = state;
      },

      toDataObject: function() {
        return _.extend(this.fields,
          {
            'create_profile': this.create_profile ? 1 : 0
          }
        );
      },
    }
  });
});
