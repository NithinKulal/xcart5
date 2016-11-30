/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Select
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.attribute-options',
    handler: function (form) {
      var element = this;
      var $element = jQuery('select', this);

      $element
        .select2(
          {
            tags: true,
            escapeMarkup: function (markup) {
              return markup;
            },
            templateSelection: function (data) {
              return '<span class="select2-selection-text" data-option-value="'+data.id+'">'+data.text+'</span>';
            }
          }
        )
        .select2Sortable({
          bindOrder: 'sortableStop',
          sortableOptions: {
            stop: function () {
              jQuery(form).change();
            }
          }
        });

      $element.data('select2').on('open', function () { this.trigger('close'); });

      $element.get(0).commonController.isChanged = function () {
        return !this.isEqualArrayValues(this.element.initialValue, this.$element.val(), this.$element);
      }
    }
  }
);
