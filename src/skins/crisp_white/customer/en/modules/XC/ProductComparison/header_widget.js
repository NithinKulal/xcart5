/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function(){
  window.product_comparison = _.wrap(window.product_comparison, function(original) {
    jQuery('.compare-checkbox input').each(function() {
      var elem = jQuery(this);
      elem.change(function(event){
        if (event.target.checked) {
          var target = $('.header_product-comparison:visible');
          if (target.length == 0) {
            target = $('.header_settings:visible');
          }

          var item = getProductRepresentationFor(this);

          if (target.length && item.element && item.element.length) {
            $(item.element).fly(target, {
              view: item.view
            });
          }
        }
      });
    });

    return original.apply(this, Array.prototype.slice.call(arguments, 1));
  });

  core.bind('updateProductComparison', function(event, data) {
    var link = $('.compare-indicator > a');
    var span = link.find('.counter');
    span.text(data.count);

    if (data.count > 1) {
      link.attr('href', link.data('target'));
    } else {
      link.attr('href', '');
    }
  });
})();