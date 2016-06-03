/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Multiselect microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    canApply: function () {
      return 0 < this.$element.filter('select.multiselect').length;
    },
    handler: function () {
      this.$element.chosen();
      this.$element.next('.chosen-container').css({
        'width':     'auto',
        'min-width': this.$element.width()
      });
    }
  }
);
