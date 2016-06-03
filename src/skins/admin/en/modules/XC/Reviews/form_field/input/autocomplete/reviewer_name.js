/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Reviewer name field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.reviewername-value .input-field-wrapper input.auto-complete',
    handler: function () {

      var input = this;
      if ('undefined' != typeof(this.autocompleteSource)) {
        jQuery(this).autocomplete('destroy');
      }

      this.autocompleteSource = function(request, response)
      {
        core.get(
          unescape(jQuery(this).data('source-url')).replace('%term%', request.term),
          null,
          {},
          {
            dataType: 'json',
            success: function (data) {
              var list = [];
              input._data = data;
              for (var i = 0;i < data.length; i++) {
                list.push({'label': data[i].label.email, 'value': data[i].label.name});
              }
              response(list);
            }
          }
        );
      };

      this.autocompleteAssembleOptions = function()
      {
          var input = this;

          return {
            source: function(request, response) {
              input.autocompleteSource(request, response);
            },
            minLength: jQuery(this).data('min-length') || 2,
            select: function( event, ui ) {
              jQuery.each(input._data, function(index, value) {
                if (value.label.name == ui.item.value) {
                  jQuery(input.form).find('#email').val(value.label.email);
                  jQuery(input.form).find('#profile-id').val(value.value);

                  return;
                }
              });
            }
          };
      };

      jQuery(this).autocomplete(this.autocompleteAssembleOptions());
    }
  }
);
