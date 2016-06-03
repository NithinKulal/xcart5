/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * section.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.SectionMixin', [], function(){
  Checkout.SectionMixin = {
    data: function() {
      return {
        name: '',
        endpoint: {},
        request_options: {
          rpc: true
        }
      }
    },

    vuex: {
      getters: {
        total_text: function(state) {
          return state.order.total_text;
        },
      },
      actions: {
        dispatchSwitch: function(state, target) {
          state.dispatch('SWITCH_SECTION', target);
        },
        registerSection: function(state) {
          state.dispatch('REGISTER_SECTION', this.name, this);
        },
        toggleComplete: function(state, value) {
          state.dispatch('TOGGLE_COMPLETE', this.name, value);
        },
        updateFields: function(state, data) {
          state.dispatch('UPDATE_SECTION_FIELDS', this.name, data);
        },
      }
    },

    created: function() {
      this.registerSection();
    },

    methods: {
      scrollToDetails: function () {
        var top = $('.checkout_fastlane_details_box:visible').offset().top - 80;
        $(window).scrollTop( top );
      },
      switchTo: function(target) {
        this.dispatchSwitch(target);
      },
      persist: function() {
        if (this.complete && !_.isEmpty(this.fields)) {
          var data = JSON.parse(JSON.stringify(this.fields));
          data[xliteConfig.form_id_name] = xliteConfig.form_id;

          core.post(
            this.endpoint,
            null,
            data,
            this.request_options
          )
          .done(
            _.bind(
              function(data){
                this.$root.$broadcast('checkout_anyPersist', {data: data});
                core.trigger('checkout.sections.address.persist', {data: data});
              },
              this
            )
          )
          .fail(function(){
            core.showError('Server connection error. Please check your Internet connection.');
          });
        }
      },
    },

    events: {
      update: function(event) {
        var isComplete = this.$children.every(function(child) {
          return _.isUndefined(child.isValid) || child.isValid;
        });

        if (isComplete !== this.complete) {
          this.toggleComplete(isComplete);
        }

        this.updateFields(event.fields);

        this.$root.$broadcast('sectionUpdate', this.name);

        if (!event.silent) {
          this.persist();
        }
      },
    },
  }
})
