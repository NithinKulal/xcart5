/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * place-order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.PlaceOrder', [], function(){
  Checkout.PlaceOrder = Vue.extend({
    name: 'place-order',

    vuex: {
      getters: {
        sections_ready: function(state) {
          return _.values(state.sections.list).every(function(section) {
            return section.complete;
          });
        },
        total_text: function(state) {
          return state.order.total_text;
        },
        notes: function(state) {
          return state.sections.list.shipping.fields.notes;
        },
        paymentData: function(state) {
          return state.sections.list.payment.fields.paymentData;
        }
      },
    },

    data: function() {
      return {
        blocked: false,
        blockers: [],
        endpoint: {
          target: 'checkout',
          action: 'checkout'
        },
        request_options: {
          async: false
        }
      };
    },

    ready: function() {
      this.assignHandlers();
    },

    methods: {
      assignHandlers: function() {
        core.bind('checkout.common.block', _.bind(
          function() {
            this.blocked = true;
          }, this)
        );

        core.bind('checkout.common.unblock', _.bind(
          function() {
            this.blocked = false;
          }, this)
        );
      },

      placeOrder: function() {
        if (this.ready) {
          this.onReadyPlaceOrder();
          this.sendForm();
        } else {
          this.onNotReadyPlaceOrder();
        }
      },

      onReadyPlaceOrder: function() {
        var state = {widget: this, state: this.ready};
        core.trigger('checkout.common.ready', state);
      },

      onNotReadyPlaceOrder: function() {
        core.trigger('checkout.common.nonready', this);
      },

      sendForm: function() {
        var data = this.getData();
        data[xliteConfig.form_id_name] = xliteConfig.form_id;

        this.$root.startLoadAnimation();

        core.post(
          this.endpoint,
          null,
          data,
          this.request_options
        )
        .done(_.bind(this.onSuccess, this))
        .fail(_.bind(this.onFail, this));
      },

      getData: function() {
        var data = {
          notes: this.notes
        };

        data = _.extend(data, this.paymentData);

        return data;
      },

      onSuccess: function(response, status, xhr){
        if (response.length > 0 && xhr.getResponseHeader('Content-Type').match(/^text\/html/i)) {
          // form based
          document.write(response);
          window.fireEvent('load');
        } else if (xhr.getResponseHeader('AJAX-Location')) {
          var url = xhr.getResponseHeader('AJAX-Location');
          if (url) {
            self.location = url;
          } else {
            self.location.reload(true);
          }
        }
      },

      onFail: function() {
        core.showError('Error on order place.');
        this.$root.finishLoadAnimation();
      },
    },

    events: {
      reloadingBlock: function(sender) {
        this.blockers.push(sender);
      },
      reloadingUnblock: function(sender) {
        this.blockers.pop();
      },
    },

    computed: {
      ready: {
        cache: false,
        get: function() {
          return this.sections_ready && !this.blocked && _.isEmpty(this.blockers);
        }
      },
      label: function() {
        return core.t('Place order') + ' ' + this.total_text;
      },
      classes: function() {
        return {
          'disabled': !this.ready
        }
      }
    },
  });
});
