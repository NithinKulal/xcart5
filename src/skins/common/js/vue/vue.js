/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('vue/vue', function () { return Vue; });

define('js/vue/vue', ['vue/vue', 'js/vue/component'], function (Vue, XLiteVueComponent) {
  function XLiteVue() {
  }

  XLiteVue.prototype.root = null;
  XLiteVue.prototype.start = function () {
    for (var componentName in this.components) if (this.components.hasOwnProperty(componentName)) {
      Vue.component(componentName, Vue.extend(this.components[componentName].definition))
    }

    this.root = new Vue({el: 'body'});
  };

  XLiteVue.prototype.components = {};
  XLiteVue.prototype.component = function (name, definition) {
    if (this.components[name]) {
      this.components[name].extend(definition);
    } else {
      this.components[name] = new XLiteVueComponent(name, definition);
    }
  };

  return new XLiteVue();
});
