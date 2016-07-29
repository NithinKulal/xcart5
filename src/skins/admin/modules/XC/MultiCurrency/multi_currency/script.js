/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Currency page routines
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function CurrencyManageForm()
{
  this.initialize();
}

CurrencyManageForm.prototype.patternCurrencyViewInfo = '.currency-view-info *';

CurrencyManageForm.prototype.initialize = function ()
{
  var obj = this;

  var defaultId = jQuery('input[name="defaultValue"]:checked').val();

  jQuery('#currency-format-' + defaultId).change(function() {
    var tz = jQuery('#trailing-zeroes');

    jQuery(obj.patternCurrencyViewInfo).trigger(
        'formatCurrencyChange',
        [
          jQuery(this).val(),
          jQuery(tz).data('e'),
          jQuery(tz).data('thousandpart'),
          jQuery(tz).data('hundredspart'),
          jQuery(tz).data('delimiter')
        ]
    );
  });

  jQuery('#data-' + defaultId + '-prefix').keyup(function(event) {
    jQuery(obj.patternCurrencyViewInfo).trigger('prefixCurrencyChange', [jQuery(this).val()]);
  });

  jQuery('#data-' + defaultId + '-suffix').keyup(function(event) {
    jQuery(obj.patternCurrencyViewInfo).trigger('suffixCurrencyChange', [jQuery(this).val()]);
  });

  jQuery('#trailing-zeroes').bind(
    'trailingZeroesClick',
    function (event) {
      jQuery(obj.patternCurrencyViewInfo).trigger('trailingZeroesClick',[jQuery(this).prop('checked')]);
    }
  ).click(function (event) {
    jQuery(this).trigger('trailingZeroesClick');
  });

  jQuery(document).ready(function () {
    jQuery('#currency-format-' + defaultId).trigger('change');

    jQuery('#data-' + defaultId + '-prefix, #data-' + defaultId + '-suffix').trigger('keyup');

    jQuery('#trailing-zeroes').trigger('trailingZeroesClick');
  });
};

CurrencyManageForm.prototype.addCurrency = function ()
{
  var obj = jQuery('#currency-id');

  jQuery(obj).closest('form').find('input[name="action"]').val('add_currency');
  jQuery(obj).closest('form').submit();
};

core.autoload(CurrencyManageForm);

var currencyManageForm = new CurrencyManageForm();