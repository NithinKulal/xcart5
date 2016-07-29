/**
 * Fly plugin for jQuery
 * v0.1
 * Animates DOMElement flight to target position. Depends on jQuery.path plugin for bezier path of flight.
 *
 * Released under the MIT license.
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
      // $(element).off('focus blur change', handler);
      $(element).on('focus blur change', handler)
    }
  }

  function handler(e) {
    var item = findParent(this);
    if (item && item.length > 0) {
        item.toggleClass('focused', ((e && e.type === 'focus') || this === document.activeElement || this.value.length > 0 || (typeof(this.selectedIndex) !== 'undefined' && this.selectedIndex !== false) ));
    }
  }

  function findParent(element) {
    var parent = null;
    if ($(element).siblings('label').length == 1) {
      parent = $(element).parent();
    } else if ($(element).closest('.floating-label').length > 0) {
      parent = $(element).closest('.floating-label');
    } else if ($(element).closest('.table-value').length > 0) {
      parent = $(element).closest('.table-value').parent();
    }

    return parent;
  }

})(jQuery);