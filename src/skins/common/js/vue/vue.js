/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('vue/vue', function () { return Vue; });

if ('undefined' !== typeof(Vuex)) {
  define('vue/vuex', function () { return Vuex; });
}

if ('undefined' !== typeof(VueLoadableMixin)) {
  define('vue/vue.loadable', function () { return VueLoadableMixin; });
}

define('js/vue/vue', ['vue/vue', 'js/vue/component'], function (Vue, XLiteVueComponent) {
  function XLiteVue() {
    this.root = null;
  }

  XLiteVue.prototype.components = {};

  XLiteVue.prototype.start = function (element) {
    for (var componentName in this.components) if (this.components.hasOwnProperty(componentName)) {
      Vue.component(componentName, Vue.extend(this.components[componentName].definition))
    }

    var elementToInit = 'body';

    if (element instanceof jQuery && element.length > 0) {
      elementToInit = element.get(0);
    }

    this.root = new Vue({el: elementToInit});
  };

  XLiteVue.prototype.component = function (name, definition) {
    if (this.components[name]) {
      this.components[name].extend(definition);
    } else {
      this.components[name] = new XLiteVueComponent(name, definition);
    }
  };

  return new XLiteVue();
});
