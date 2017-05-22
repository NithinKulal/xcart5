/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Top sellers controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function TopSellers() {
  TopSellers.superclass.constructor.apply(this, arguments);

  var self = this;
  core.bind('update-top-sellers', function (event, params) {
    self.load(params);
  });
}

extend(TopSellers, ALoadable);

TopSellers.autoload = function () {
  jQuery('.top-sellers-container').each(
    function () {
      new TopSellers(this);
    }
  );
};

TopSellers.initialRequested = false;

TopSellers.prototype.shadeWidget = true;

TopSellers.prototype.base = '.top-sellers-container';

TopSellers.prototype.widgetTarget = 'top_sellers';

TopSellers.prototype.widgetClass = 'XLite\\View\\TopSellers';

TopSellers.prototype.postprocess = function (isSuccess, initial) {
  if (isSuccess) {
    $('.top-sellers-selectors.period-selectors a', this.base).click(function () {
      core.trigger('update-top-sellers', {time_interval: $(this).data('interval')});
      return false;
    });

    $('.top-sellers-selectors.availability-selectors a', this.base).click(function () {
      core.trigger('update-top-sellers', {availability: $(this).data('availability')});
      return false;
    });

    console.log('availability-selectors');
  }
};

core.autoload(TopSellers);

jQuery(document).ready(function () {
});