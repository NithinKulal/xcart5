/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Tooltip widget JS class
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function ($) {

  // Tooltips in customer area
  jQuery('.tooltip-main').each(
    function () {
      attachTooltip(
        jQuery('i', this),
        jQuery('.help-text', this).hide().html()
      );
    }
  );

  var startTooltip = function (element) {
    var helpId = $(element).data('help-id');
    var params = {};

    if ($(element).parents('.ui-widget-content:first').length) {
      params.viewport = ('#' + $(element).parents('.ui-widget-content:first').attr('id'));
    }

    if (helpId) {
      content = $('#' + helpId).html();
      if (content) {
        params.content = content;
        $(element).popover(params);
      }

    } else {
      params.container = element;
      $(element).popover(params);
    }
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
