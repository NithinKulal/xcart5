/**
 * Floating label plugin for jQuery. 
 * Version 1.1.
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function($) {

  var defaults = {};

  options = {};

  // jQuery plugin definition
  $.fn.floatingLabel = function(options) {
    if ('undefined' === typeof(options)) {
      options = {};
    }

    pluginOptions = _.defaults(options, defaults);

    if (this.length > 0) {
      this.each(function() {
        assignHandlers(this);

        // capture initial state
        handler.apply(this, [null]);
      })
    }

    return this;
  };

  function assignHandlers(element) {
    if (!element.floatingLabel) {
      element.floatingLabel = true;
      $(element).on('focus blur change', handler);
    }
  }

  function handler(e) {
    var item = findParent(this);
    if (item && item.length > 0) {
        item.toggleClass('focused', ((e && e.type === 'focus') || this.value.length > 0 || (typeof(this.selectedIndex) !== 'undefined' && this.selectedIndex !== false) ));
    }
  }

  function findParent(element) {
    var parent = null;
    if ($(element).siblings('label:not(.form-control-label)').length == 1) {
      parent = $(element).parent();
    } else if ($(element).closest('.floating-label').length > 0) {
      parent = $(element).closest('.floating-label');
    } else if ($(element).closest('.table-value').length > 0) {
      parent = $(element).closest('.table-value').parent();
    }

    return parent;
  }

})(jQuery);