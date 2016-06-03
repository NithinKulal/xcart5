/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-field-wrapper input.auto-complete',
    handler: function () {

      if ('undefined' == typeof(this.autocompleteSource)) {
        this.autocompleteSource = function(request, response)
        {
          core.get(
            decodeURI(jQuery(this).data('source-url')).replace('%term%', request.term),
            null,
            {},
            {
              dataType: 'json',
              success: function (data) {
                response(data);
              }
            }
          );
        }
      }

      if ('undefined' == typeof(this.autocompleteAssembleOptions)) {
        this.autocompleteAssembleOptions = function()
        {
          var input = this;

          return {
            source: function(request, response) {
              input.autocompleteSource(request, response);
            },
            minLength: jQuery(this).data('min-length') || 2,
          };
        }
      }

      jQuery(this).autocomplete(this.autocompleteAssembleOptions());
    }
  }
);

