/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Date field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    canApply: function () {
      return this.$element.is('input.datepicker');
    },
    handler: function () {
      var options = core.getCommentedData(this.$element.parents('.input-field-wrapper'));
      this.$element.datepicker({
        dateFormat: options.dateFormat
      });
    }
  }
);
