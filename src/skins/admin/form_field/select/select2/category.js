/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-category-select2 select',
    handler: function () {
      $(this).select2({
        language: {
          noResults: function(){
            return core.t('No results found.');
          },
          searching: function () {
            return core.t('Searching...');
          }
        },
        escapeMarkup: function (markup) {
          return markup;
        },
        dropdownParent: $(this).parents('.search-conditions-box:first')
      });
    }
  }
);
