/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var box = {};

// Shade block with content
function shadeBlock()
{
  if (0 != jQuery(box).length) {
    var overlay = jQuery(document.createElement('div'))
      .addClass('single-progress-mark');
    jQuery(document.createElement('div'))
      .appendTo(overlay);

    overlay.width(box.outerWidth())
      .height(box.outerHeight());

    overlay.appendTo(box);
  }
}

function unshadeBlock()
{
  if (0 != jQuery(box).length) {
    jQuery(box).find('.single-progress-mark').remove();

    box = {};
  }
}

var suggestionsCash = new CacheEngine();

CommonForm.elementControllers.push(
  {
    pattern: '.input-field-wrapper input.auto-complete',
    handler: function () {

      if ('undefined' == typeof(this.autocompleteSource)) {
        this.autocompleteSource = function(request, response)
        {
          unshadeBlock();

          box = jQuery(this).parent('span');

          var url = unescape(jQuery(this).data('source-url')).replace('%term%', request.term);
          if (!suggestionsCash.has(url)) {
            shadeBlock();

            core.get(
              url,
              null,
              {},
              {
                dataType: 'json',
                success: function (data) {
                  suggestionsCash.add(url, data);
                  response(data);

                  unshadeBlock();
                }
              }
            );
          } else {
            response(suggestionsCash.get(url));
          }
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
            close: function() {jQuery(this).keyup()},
            select: function() {jQuery(this).dblclick()}
          };
        }
      }

      jQuery(this).autocomplete(this.autocompleteAssembleOptions());
    }
  }
);

