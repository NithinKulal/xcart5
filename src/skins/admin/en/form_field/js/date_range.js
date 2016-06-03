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

      if (config.customShortcuts && config.customShortcuts.length > 0) {

        var now = new Date();
        var format = config.format || 'YYYY-MM-YY';

        for (i = 0; i < config.customShortcuts.length; i++) {

          var sh = config.customShortcuts[i];

          if ('today' == sh.name) {
            config.customShortcuts[i].dates = function() {
              return [moment().toDate(format),moment().toDate(format)];
            }

          } else if ('this week' == sh.name) {
            config.customShortcuts[i].dates = function() {
              return [moment().startOf('week').toDate(), moment().toDate()];
            }

          } else if ('this month' == sh.name) {
            config.customShortcuts[i].dates = function() {
              return [moment().startOf('month').toDate(), moment().toDate()];
            }

          } else if ('this quarter' == sh.name) {
            config.customShortcuts[i].dates = function() {
              return [moment().startOf('quarter').toDate(), moment().toDate()];
            }

          } else if ('this year' == sh.name) {
            config.customShortcuts[i].dates = function() {
              return [moment().startOf('year').toDate(), moment().toDate()];
            }
          }
        }
      }
      if (this.$element.data('end-date')) {
        config.endDate = this.$element.data('end-date');
      }
      this.$element.dateRangePicker(config);
    }
  }
);
