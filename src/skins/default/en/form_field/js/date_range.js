/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Date range field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    canApply: function () {
      return this.$element.is('input.date-range');
    },
    handler: function() {
      var config = this.$element.data('datarangeconfig') || {};
      config.seperator = ' ~ ';
      config.language = 'en';
      this.$element.dateRangePicker(config);
    }
  }
);
