/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Tooltip widget JS class
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function ($) {

  var startTooltip = function (element) {
    $(element).popover({container: element});
  };

  if (window.Vue) {
    Vue.directive('xlite-tooltip', {
      bind: function () {
        startTooltip(this.el);
      }
    });
  } else {
    $(function () {
      $('[data-toggle="popover"]').each(function () {
        startTooltip(this);
      })
    });

    core.microhandlers.add(
      'tooltip',
      '[data-toggle="popover"]',
      function () {
        startTooltip(this);
      }
    );
  }

})(jQuery);
