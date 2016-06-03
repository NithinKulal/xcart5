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
      return 0 < this.$element.filter('select.tags').length;
    },
    handler: function () {
      this.$element.data('jqv', {validateNonVisibleFields: true});
      this.$element.chosen();
      this.$element.next('.chosen-container').css('min-width', this.$element.width());

      this.$element.insertAfter(this.$element.next('.chosen-container'));

      this.$element.bind('invalid', function () {
        jQuery(this).siblings('.chosen-container').find('input').get(0).click();
      });

      jQuery('.chosen-container .search-choice').live('click', function () {
        jQuery('.search-choice-close', this).trigger('click.chosen');
      });
   }
  }
);
