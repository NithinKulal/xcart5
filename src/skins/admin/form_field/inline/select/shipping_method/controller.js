/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Inline form field common controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function OrderShippingMethod(base)
{
  ALoadable.apply(this, arguments);
}

extend(OrderShippingMethod, ALoadable);

OrderShippingMethod.autoload = function()
{
  jQuery('.shipping-method-selector').each(
    function() {
      new OrderShippingMethod(this);
    }
  );
}

OrderShippingMethod.initialRequested = false;

OrderShippingMethod.prototype.shadeWidget = true;

OrderShippingMethod.prototype.widgetTarget = 'order';

OrderShippingMethod.prototype.widgetClass = '\\XLite\\View\\FormField\\Inline\\Select\\ShippingMethod';

OrderShippingMethod.prototype.postprocess = function(isSuccess)
{
  ALoadable.prototype.postprocess.apply(this, arguments);

  if (isSuccess) {
    core.bind('recalculateshipping', _.bind(this.handleRecalculateShipping, this));

    this.base.find('select').change(_.bind(this.handleChange, this));

    if (this.base.find('select').data('request-options')) {
      this.shade();

      if (!OrderShippingMethod.initialRequested) {
        this.loadMethods();
        OrderShippingMethod.initialRequested = true;
      }
    }
  }
}

OrderShippingMethod.prototype.handleRecalculateShipping = function(event, data)
{
  var select = this.base.find('select');

  // Clean
  jQuery('option', select.get(0))
    .filter(
      function() {
        return this.value > 0 && this.value != 'deleted';
      }
    )
    .remove();

  // Add
  var selector = select.get(0);

  _.each(
    data.options,
    function(value, idx) {
      var option = new Option(value.name, idx);
      jQuery(option).data('value', value.fullName);
      selector.add(option);
    }
  );

  var value = select.data('value');
  var isSelected = false;
  _.each(
    jQuery('option', select.get(0)),
    function(option, idx) {
      if (option.value == value) {
        option.selected = true;
        jQuery(option).attr('selected', 'selected');
        select.get(0).selectedIndex = idx;
        isSelected = true;

      } else {
        option.selected = false;
        jQuery(option).removeAttr('selected');

      }
    }
  );

  if (!isSelected) {
    select.get(0).options[0].selected = true;
    select.get(0).selectedIndex = 0;
    jQuery(select.get(0).options[0]).attr('selected', 'selected');
    select.data('value', select.get(0).options[0].value);

    select.parents('.inline-field').get(0).saveField();
  }

  this.unshade();
}

OrderShippingMethod.prototype.handleChange = function(event)
{
  var select = this.base.find('select');
  jQuery('select#shippingid').data('value', select.val());
}

OrderShippingMethod.prototype.loadMethods = function()
{
  var form = jQuery('form.order-operations').get(0);

  var action = form.elements.namedItem('action');
  var old = action.value;
  action.value = 'recalculate_shipping';
  form.commonController.submitBackground();
  action.value = old;
}

OrderShippingMethod.prototype.assignWaitOverlay = function(base)
{
  var overlay = jQuery('<div class="single-progress-mark"><div></div></div>');
  base.append(overlay);
  base.css('position', 'relative');

  return overlay;
}

OrderShippingMethod.prototype.unassignWaitOverlay = function(base)
{
  base.find('.single-progress-mark').remove();
}

OrderShippingMethod.prototype.getShadeBase = function()
{
  return this.base.find('.field');
};

core.autoload(OrderShippingMethod);
