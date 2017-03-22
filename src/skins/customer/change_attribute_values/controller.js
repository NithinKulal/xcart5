/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * AttributeValues controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Main widget
 */

function AttributeValues (base) {
  this.callSupermethod('constructor', arguments);
}

extend(AttributeValues, ALoadable);

AttributeValues.prototype.shadeWidget = true;
AttributeValues.prototype.widgetTarget = 'change_attribute_values';
AttributeValues.prototype.widgetClass = 'XLite\\View\\Product\\AttributeValues';

AttributeValues.prototype.postprocess = function (isSuccess) {
  if (isSuccess) {
    var self = this;
    jQuery('select', this.base).bind('change', function () {
      self.load();
    });
  }
};

AttributeValues.prototype.getParams = function(params)
{
  params = this.callSupermethod('getParams', arguments);

  var form = jQuery(this.base).closest('form');

  params.source = form.get(0).source.value;
  params.item_id = form.get(0).item_id.value;

  params.attribute_values = [];
  jQuery('select[name^="attribute_values"]', form).each(function () {
    params['attribute_values[' + jQuery(this).data('attributeId') + ']'] = jQuery(this).val();
  });

  return params;
};

core.microhandlers.add(
  'AttributeValues',
  'ul.attribute-values',
  function (event, item) {
    new AttributeValues(item);
  }
);
