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
}