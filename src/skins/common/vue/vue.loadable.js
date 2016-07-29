(function () {
  var initialCompiled = false;

  var cache = {};

  var VueLoadableMixin = {
    created: function () {
      Vue.util.defineReactive(this, '$reloading', false);
    },
    compiled: function () {
      if (this.$options.loadable && this.$options.loadable.loadOnCompile && !initialCompiled) {
        this.$reload();
        initialCompiled = true;
      }
    },

    methods: {
      $reload: function () {
        var loader = this.$options.loadable.loader;
        if (loader) {
          this.$reloading = true;
          var promise = null;

          if (this.$options.loadable.cacheSimultaneous) {
            if (!_.has(cache, this.$options.name)) {
              cache[this.$options.name] = loader.call(this, arguments);
            }
            promise = cache[this.$options.name];
          } else {
            promise = loader.call(this, arguments);
          }

          if (promise && typeof promise.then === 'function') {
            promise.then(this._resolve, this._reject);
          }
        }
      },
      _resolve: function (data) {
        var uuid = _.uniqueId();
        var self = this;

        core.bind(['resources.ready', 'resources.empty'], _.bind(
          function(event, args){
            if (args.uuid === uuid) {
              var oldData = JSON.parse(JSON.stringify(this.$data));
              this._updateComponent(data);
              if (this.$options.loadable.transferState) {
                this.$data = oldData;
              }
              this.$reloading = false;
              this.$options.loadable.resolve.apply(this, [data]);

              delete cache[this.$options.name];
            }
          },
          this)
        );

        core.parseResources(data, uuid);
      },
      _reject: function (data) {
        this.$reloading = false;
        this.$options.loadable.reject.apply(this, [data]);

        delete cache[this.$options.name];
      },
      _updateComponent: function(html) {
        var element = this._parseTemplate(html);
        this.$options.template = Vue.util.extractContent(element, true);
        this._isCompiled = this._isAttached = this._isReady = false;
        this.$mount(this.$el);
      },
      _parseTemplate: function(html) {
        if (this.$options.loadable.parser) {
          var element = this.$options.loadable.parser.apply(this, [html]);
        } else {
          var temp = document.createElement('div');
          temp.innerHTML = html;

          var elements = temp.querySelectorAll('[is=' + this.$options.name + ']');
          if (elements.length == 0) {
            var elements = temp.querySelectorAll(this.$options.name);
          }

          var element = elements[0];
        }

        return element;
      }
    }
  }

  if(typeof exports === 'object' && typeof module === 'object') {
    module.exports = VueLoadableMixin
  } else if(typeof define === 'function' && define.amd) {
    define(function () { return VueLoadableMixin })
  } else if (typeof window !== 'undefined') {
    window.VueLoadableMixin = VueLoadableMixin
  }
})();
