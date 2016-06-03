/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Language controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */

function CountrySelect(base)
{
  this.callSupermethod('constructor', arguments);
}

extend(CountrySelect, AController);

// Controller name
CountrySelect.prototype.name = 'CountrySelect';

// Find pattern
CountrySelect.prototype.findPattern = 'select[id^="country-code-selector"]';

// Initialize controller
CountrySelect.prototype.initialize = function()
{
  jQuery(this.base).change(_.bind(
      function(event, box) {
        this.changeCountry(box)
      },
      this
  ));
};

// Toggle view mode
CountrySelect.prototype.changeCountry = function(elem)
{
  if (typeof currenciesByCountry[jQuery(this.base).val()] != 'undefined') {
    jQuery('div[id^="currency-selector-container"]').find('select[id^="currency-code-selector"]').val(
        currenciesByCountry[jQuery(this.base).val()]
    );
  }
};

core.autoload(CountrySelect);