/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Trial notice button and popup controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonLocationSelect()
{
  PopupButtonLocationSelect.superclass.constructor.apply(this, arguments);
}

// New POPUP button widget extends POPUP button class
extend(PopupButtonLocationSelect, PopupButton);

// New pattern is defined
PopupButtonLocationSelect.prototype.pattern = '.location-select';

PopupButtonLocationSelect.prototype.enableBackgroundSubmit = true;

decorate(
  'PopupButtonLocationSelect',
  'callback',
  function(selector) {
  	UpdateStatesList();

    jQuery('form', selector).each(
      function() {
        jQuery(this).commonController(
          'enableBackgroundSubmit',
          undefined,
          function (event, xhr) {

            popup.close();

            xhr.XMLHttpRequest.done(
              function(){
                jQuery('.btn.location-select span.country')
                  .text(decodeURIComponent(escape(xhr.XMLHttpRequest.getResponseHeader('Location-data'))));
              }
            );

            return false;
          }
        );
      }
    );
  }
);

// Autoloading new POPUP widget
core.autoload(PopupButtonLocationSelect);