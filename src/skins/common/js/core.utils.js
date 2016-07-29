/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Set of utility functions, such as hashing and other
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
window.core.utils = {

	/**
	 * Generates hash from given value using md5 algorithm
	 * @param  mixed	value 	 Value to be hased
	 * @return string       	 MD5 hash of value
	 */
	hash: function(value) {
		return objectHash.MD5(value);
	}
};

window.await = function (promises, callback) {
  jQuery.when.apply(jQuery, promises).done(callback);
};

// Shim array.prototype.find for es5
if (!Array.prototype.find) {
  Object.defineProperty(Array.prototype, "find", {
    value: function(predicate) {
      if (this === null) {
        throw new TypeError('Array.prototype.find called on null or undefined');
      }
      if (typeof predicate !== 'function') {
        throw new TypeError('predicate must be a function');
      }
      var list = Object(this);
      var length = list.length >>> 0;
      var thisArg = arguments[1];
      var value;

      for (var i = 0; i < length; i++) {
        value = list[i];
        if (predicate.call(thisArg, value, i, list)) {
          return value;
        }
      }
      return undefined;
    }
  });
}