/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Wholesale functions
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function getWholesaleParams(product)
{
  return {
    quantity: jQuery(".product-qty input[type='text']").val()
  };
}

function getWholesaleTriggers()
{
  return ".product-qty input[type='text']";
}

function getWholesaleShadowWidgets()
{
    return '.widget-fingerprint-product-price';
}

function bindWholesaleTriggers()
{
  var handler = function ($this) {
    core.trigger('update-product-page', jQuery('input[name="product_id"]', $this).val());
  };

  var timer;
  var $this = jQuery(".product-qty.wholesale-price-defined input[type='text']").closest('form')

  if ($this) {
    jQuery(".product-qty.wholesale-price-defined input[type='text']")
      .one(
        'input',
        function (event) {
          clearTimeout(timer);
          timer = setTimeout(
            function () {
              var ctrl = event.currentTarget.commonController;
              if (ctrl.isChanged() && ctrl.validate(true)) {
                ctrl.saveValue();
                handler($this);
              }
            },
            2000
          );
      });
  }
}

core.registerWidgetsParamsGetter('update-product-page', getWholesaleParams);
core.registerWidgetsTriggers('update-product-page', getWholesaleTriggers);
core.registerTriggersBind('update-product-page', bindWholesaleTriggers);
core.registerShadowWidgets('update-product-page', getWholesaleShadowWidgets);
core.registerShadowWidgets('update-product-page', function(){
  return '.widget-fingerprint-product-wholesale-prices';
});
core.registerShadowWidgets('update-product-page', function(){
  return '.widget-fingerprint-wholesale-quantity';
});
