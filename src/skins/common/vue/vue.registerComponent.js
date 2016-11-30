(function () {
  function install (Vue) {
    Vue.registerComponent = function (parent, child, name) {
      name = name || child.name;
      if (typeof(parent) === 'function' && typeof(child) === 'function') {
        var record = {};
        record[name] = child;
        parent = parent.extend({
          components: _.extend(parent.options.components, record)
        });
      } else {
        console.groupCollapsed("[vue.registerComponent] error");
        console.error('Given argument is not a object constructor');
        console.error(arguments);
        console.groupEnd();
      }
    }
  }

  if (typeof exports == "object") {
    module.exports = install
  } else if (typeof define == "function" && define.amd) {
    define([], function(){ return install })
  } else if (window.Vue) {
    Vue.use(install)
  }
})();
