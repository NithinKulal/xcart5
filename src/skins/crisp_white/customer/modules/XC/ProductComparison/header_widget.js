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

  core.loadLanguageHash(core.getCommentedData('.header_product-comparison'));

  core.bind('updateProductComparison', function(event, data) {
    var indicator = $('.compare-indicator');
    var link = $('.compare-indicator > a');
    var span = link.find('.counter');
    var old_count = parseInt(span.first().text());

    span.text(data.count > 0 ? data.count : '');

    if (data.count > 1) {
      indicator.removeClass('disabled');
      link.attr('href', link.data('target'));
      link.attr('title', core.t('Go to comparison table'));
    } else {
      indicator.addClass('disabled');
      link.removeAttr('href');
      link.attr('title', core.t('Please add another product to comparison'));
    }

    if (data.count > 0 && (data.count > old_count || isNaN(old_count) || indicator.hasClass('recently-updated'))) {
      indicator.addClass('recently-updated');
    } else {
      indicator.removeClass('recently-updated');
    }

    core.trigger('checkHeaderSettingsRecentlyUpdated');
  });

  core.bind('afterPopupPlace', product_comparison);

})();

jQuery(function () {
  if ('undefined' !== typeof HeaderSettingsController) {
    var save = HeaderSettingsController.prototype.isRecentlyUpdated;

    HeaderSettingsController.prototype.isRecentlyUpdated = function()
    {
      return save() || $('.compare-indicator').hasClass('recently-updated');
    };
  }
});
