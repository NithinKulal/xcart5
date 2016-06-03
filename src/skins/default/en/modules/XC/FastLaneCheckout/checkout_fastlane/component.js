/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * component.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout = {
  dependencies: {},
  loadableCache: {},
  define: function(name, deps, callback) {
    var self = this;
    var promise = new jQuery.Deferred();
    this.dependencies[name] = promise;

    jQuery(document).ready(function(){
      if (_.isNull(deps) || _.isEmpty(deps)) {
        self.resolve(promise, callback);
      } else {
        self.require(deps, function() {
          self.resolve(promise, callback);
        });
      }
    });
  },
  resolve: function (promise, callback) {
    callback();
    promise.resolve();
  },
  require: function(names, callback) {
    var self = this;
    var promises = names.map(function(name){
      if (_.isUndefined(self.dependencies[name])) {
        throw new Error("[require] Cannot find module " + name + " definition.");
      }
      return self.dependencies[name];
    });

    await(promises, callback);
  },

  requireAll: function(callback) {
    await(_.values(this.dependencies), callback);
  },
};

jQuery(document).ready(function(){
  Vue.config.devtools = true;
  Vue.config.debug = true;

  Vue.directive('data', {
    update: function() {
      var object = JSON.parse(this.expression);
      for (var key in object) {
        this.vm.$set(key, object[key]);
      }
    },
  });

  core.loadLanguageHash(core.getCommentedData('.checkout_fastlane_container'));

  Checkout.requireAll(function() {
    Checkout.App = Vue.extend({
      name: 'checkout',
      replace: false,

      created: function() {
        core.trigger('checkout.main.initialize');
      },

      ready: function() {
        this.finishLoadAnimation();
        this.assignGlobalListeners();
        core.trigger('checkout.main.ready');
      },

      components: {
        Sections: Checkout.Sections,
        Navigation: Checkout.Navigation
      },

      methods: {
        startLoadAnimation: function() {
          $(this.$el).addClass('reloading');
        },
        finishLoadAnimation: function() {
          $(this.$el).removeClass('reloading');
        },
        assignGlobalListeners: function() {
          core.bind('updatecart', _.bind(this.handleUpdateCart, this));
        },
        handleUpdateCart: function(event, data) {
          this.$broadcast('global_updatecart', data);
        }
      },

      store: Checkout.Store
    });

    new Checkout.App({ el: '.checkout_fastlane_container' });
  });
});