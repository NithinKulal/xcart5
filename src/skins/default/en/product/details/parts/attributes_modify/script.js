/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product attributes functions
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Get product attribute element by name
function product_attribute(name_of_attribute)
{
  var e = jQuery('form[name="add_to_cart"] :input').filter(
    function() {
      return this.name && this.name.search(name_of_attribute) != -1;
    }
  );

  return e.get(0);
}

var textAttrCache = [];

function getAttributeValuesParams(product)
{
  var activeAttributeValues = '';
  var base = '.product-info-' + product.product_id;

  jQuery("ul.attribute-values input[type=checkbox]", jQuery(base).last()).each(function(index, elem) {
    activeAttributeValues += jQuery(elem).data('attribute-id') + '_';
    activeAttributeValues += jQuery(elem).is(":checked") ?  jQuery(elem).val() : jQuery(elem).data('unchecked');
    activeAttributeValues += ',';
  });

  jQuery("ul.attribute-values select", jQuery(base).last()).each(function(index, elem) {
    activeAttributeValues += jQuery(elem).data('attribute-id') + '_' + jQuery(elem).val() + ',';
  });

  jQuery("ul.attribute-values textarea", jQuery(base).last()).each(function(index, elem) {
    textAttrCache[jQuery(elem).data('attribute-id')] = jQuery(elem).val();
  });

  return {
    attribute_values: activeAttributeValues
  };
}

/**
 * Product attributes triggers are inputs and selectors
 * of the attribute-values block
 *
 * @returns {String}
 */
function getAttributeValuesTriggers()
{
  return 'ul.attribute-values input, ul.attribute-values select';
}

function getAttributeValuesShadowWidgets()
{
  return '.widget-fingerprint-product-price';
}

function bindAttributeValuesTriggers()
{
  var handler = function ($this) {
    core.trigger('update-product-page', jQuery('input[name="product_id"]', $this).val());
  };
  var $this = jQuery("ul.attribute-values").closest('form')

  jQuery("ul.attribute-values input[type='checkbox']").unbind('change').bind('change', function (e) {handler($this)});
  jQuery("ul.attribute-values select").unbind('change').bind('change', function (e) {handler($this)});

  jQuery("ul.attribute-values textarea", $this).each(function(index, elem) {
    jQuery(elem).val(textAttrCache[jQuery(elem).data('attribute-id')]);
  });
}

core.registerWidgetsParamsGetter('update-product-page', getAttributeValuesParams);
core.registerWidgetsTriggers('update-product-page', getAttributeValuesTriggers);
core.registerTriggersBind('update-product-page', bindAttributeValuesTriggers);
core.registerShadowWidgets('update-product-page', getAttributeValuesShadowWidgets);
core.registerShadowWidgets('update-product-page', function() {
  return '.widget-fingerprint-common-attributes';
});
