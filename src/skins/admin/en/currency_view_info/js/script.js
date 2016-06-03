/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Currency page routines
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function CurrencyViewInfo()
{
  this.initialize();
}

CurrencyViewInfo.prototype.initialize = function ()
{
  jQuery('.currency-view-info .currency.currency-zero .format').bind(
    'formatCurrencyChange',
    function(e, value, exp, thousand, hundreds, delimiter) {
      var format = value.split(delimiter);

      jQuery(this).html(thousand + format[0] + hundreds);
    }
  );

  jQuery('.currency-view-info .currency.currency-zero .decimal').bind(
    'formatCurrencyChange',
    function(e, value, exp, thousand, hundreds, delimiter) {
      if (0 == exp) {
        jQuery(this).html('');
      } else {
        var format = value.split(delimiter);

        jQuery(this).html(format[1] + (new Array(exp + 1).join('0')));
      }
    }
  ).bind(
    'trailingZeroesClick',
    function (e, value) {
      if (value) {
        jQuery(this).hide();
      } else {
        jQuery(this).show();
      }
    }
  );

  jQuery('.currency-view-info .currency .prefix').bind('prefixCurrencyChange', function(e, value) {jQuery(this).html(htmlspecialchars(value, null, null, false));});

  jQuery('.currency-view-info .currency .suffix').bind('suffixCurrencyChange', function(e, value) {jQuery(this).html(htmlspecialchars(value, null, null, false));});
}

core.autoload(CurrencyViewInfo);
