/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * section.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define('checkout_fastlane/sections/section_mixin', [], function(){
  return {
    data: function() {
      return {
        name: '',
        endpoint: {},
        request_options: {
          rpc: true
        }
      }
    },

    props: [
      'nextLabel'
    ],

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
      }
    },

    created: function() {
      this.registerSection();
    },

    methods: {
      switchTo: function(target) {
        this.dispatchSwitch(target);
      },
      persist: function(fields, force, sender) {
        if ((force || this.complete) && !_.isEmpty(fields)) {
          this.$broadcast('beforeSectionPersist', {sender: sender});
          
          var data = JSON.parse(JSON.stringify(fields));
          data[xliteConfig.form_id_name] = xliteConfig.form_id;

          $.when(this.xhr).then(_.bind(function(){

            this.xhr = core.post(
              this.endpoint,
              null,
              data,
              this.request_options
            )
            .done(
              _.bind(function(data){
                core.trigger('checkout.sections.' + this.name + '.persist', {status: true, data: data, sender: sender});
                this.$broadcast('sectionPersist', {status: true, data: data, sender: sender});
              }, this)
            )
            .fail(
              _.bind(function(data){
                core.showError('Server connection error. Please check your Internet connection.');
                core.trigger('checkout.sections.' + this.name + '.persist', {status: false, data: null, sender: sender});
                this.$broadcast('sectionPersist', {status: false, data: data, sender: sender});
              }, this)
            );

          }, this));
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

        this.$root.$broadcast('sectionUpdate', this.name);

        if (!event.silent) {
          this.persist(event.fields, event.force, event.sender);
        }
      },
    },
  }
})
