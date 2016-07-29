/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * AMD
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var CoreAMD = {
  pending: {},

  define: function (name, dependencies, callback) {

    if (_.isFunction(dependencies)) {
      callback = dependencies;
      dependencies = [];
    }

    if (_.isString(dependencies)) {
      dependencies = [dependencies];
    }

    if (dependencies === undefined) {
      dependencies = [];
    }

    if (callback === undefined) {
      callback = function () {};
    }

    var moduleDeferred = this._createDeferred(name);

    jQuery
      .when.apply(jQuery, dependencies.map(_.bind(this._createDeferred, this)))
      .then(function () {
        moduleDeferred.resolve(callback.apply(window, arguments));
      })
  },

  getUnresolvedDependencies: function () {
    var result = [];
    for (var name in this.pending) if (this.pending.hasOwnProperty(name)) {
      if (this.pending[name].state() === 'pending') {
        result.push(name);
      }
    }

    return result;
  },

  _createDeferred: function (name) {
    if (this.pending[name] === undefined) {
      this.pending[name] = new jQuery.Deferred();
    }

    return this.pending[name];
  }
};

if (window.define) {
  window._define = window.define;
}
window.define = _.bind(CoreAMD.define, CoreAMD);

define('js/jquery', function () { return jQuery; });
define('js/underscore', function () { return _; });

jQuery(function () {
  define('ready');

  var unresolvedDependencies = CoreAMD.getUnresolvedDependencies();
  if (unresolvedDependencies.length) {
    console.warn('Unresolved dependecies', unresolvedDependencies);
  }
});