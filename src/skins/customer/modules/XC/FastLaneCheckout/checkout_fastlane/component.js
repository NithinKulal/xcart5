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

  if (core.isDeveloperMode) {
    Vue.config.debug = true;
    Vue.config.devtools = true;
  }

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
        core.trigger('checkout.main.postprocess');
      },

      ready: function() {
        this.$broadcast('checkStartSection');
        this.finishLoadAnimation();
        this.assignGlobalListeners();
        core.trigger('checkout.main.ready');
      },

      components: {
        Sections: Checkout.Sections,
        Navigation: Checkout.Navigation,
      },

      methods: {
        getState: function() {
          return this.$store.state;
        },
        startLoadAnimation: function() {
          $('body').addClass('reloading reloading-animated');
          // if ($('#content').length) {
          //   $('#content').addClass('reloading');
          // };
        },
        finishLoadAnimation: function() {
          $('body').removeClass('reloading reloading-animated');
          // if ($('#content').length) {
          //   $('#content').removeClass('reloading');
          // };
          $(this.$el).removeClass('reloading reloading-animated');
        },
        reloadBlock: function(blockName) {
          if (jQuery(blockName).length) {
            jQuery(blockName).get(0).__vue__.$reload();
          } else {
            console.error('Trying to reload undefined checkout block ' + blockName);
          };
        },
        assignGlobalListeners: function() {
          core.bind('updatecart', _.bind(this.broadcastCoreEvent('global_updatecart'), this));
          core.bind('loginexists', _.bind(this.broadcastCoreEvent('global_loginexists'), this));
          core.bind('invalidElement', _.bind(this.broadcastCoreEvent('global_invalidelement'), this));
          core.bind('selectcartaddress', _.bind(this.broadcastCoreEvent('global_selectcartaddress'), this));
        },
        broadcastCoreEvent: function(name) {
          return function(event, data) {
            this.$broadcast(name, data);
          }
        },
      },

      store: Checkout.Store
    });

    Checkout.instance = new Checkout.App({ el: '.checkout_fastlane_container' });
  });
});
