/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('js/vue/component', ['js/underscore'], function (_) {

  var XLiteVueComponent = function (name, definition) {
    this.name = name;
    this.definition = definition;
  };

  XLiteVueComponent.prototype.extend = function (definition) {
    this._createExtend('props', [], this._extendProps)(definition);
    this._createExtend('methods', {}, this._extendMethods)(definition);
    this._createExtend('directives', {}, this._extendDirectives)(definition);
  };

  XLiteVueComponent.prototype._createExtend = function (field, def, extend) {
    return _.bind(function (definition) {
      if (definition[field]) {
        if (!this.definition[field]) {
          this.definition[field] = def;
        }

        _.bind(extend, this)(definition);
      }
    }, this);
  };

  XLiteVueComponent.prototype._extendProps = function (definition) {
    this.definition.props = this.definition.props.concat(definition.props);
  };

  XLiteVueComponent.prototype._extendMethods = function (definition) {
    var methods = definition.methods;

    for (var methodName in methods) if (methods.hasOwnProperty(methodName)) {
      if (this.definition.methods[methodName]) {
        methods[methodName].parent = this.definition.methods[methodName];
      }

      this.definition.methods[methodName] = methods[methodName];
    }
  };

  XLiteVueComponent.prototype._extendDirectives = function (definition) {
    this.definition.directives = _.extend(this.definition.directives || {}, definition.directives);
  };

  return XLiteVueComponent;
});
