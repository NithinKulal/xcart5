/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Order payment method data popup
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-popup.inline-payment-method-data',
    handler: function () {

      var field = jQuery(this);

      this.getPopupURL = function()
      {
        var url = field.data('popup-url');

        jQuery('.order-info .inline-payment-method-data :input').each(
          function(idx, elm) {
            url += (-1 == url.search(/\?/) ? '?' : '&') + elm.name + '=' + encodeURIComponent(elm.value);
          }
        );

        return url;
      }
    }
  }
);

function OrderPaymentMethodDataView()
{
  OrderPaymentMethodDataView.superclass.constructor.apply(this, arguments);

  core.bind('afterPopupPlace', _.bind(this.handlePopupOpen, this));
}

extend(OrderPaymentMethodDataView, Base);

OrderPaymentMethodDataView.prototype.lastForm = null;

OrderPaymentMethodDataView.prototype.changeCount = 0;

OrderPaymentMethodDataView.prototype.handlePopupOpen = function()
{
  var box = this.getBox();

  if (box.length > 0) {

    box.find('form')
      .submit(_.bind(this.handleFormSubmit, this))
      .bind('beforeSubmit', function(event) { event.result = false; });

    box.find('form :input').each(_.bind(this.setValues, this));

    box.find('form :input').filter(':visible').eq(0).focus();

    core.bind('popup.close', _.bind(this.handleClosePopup, this));
  }
}

OrderPaymentMethodDataView.prototype.handleFormSubmit = function(event)
{
  var form = this.getBox().find('form').get(0);
  if (form.commonController.validate()) {
    this.changeCount = 0;
    this.getBox().find('form :input').each(_.bind(this.syncField, this));
    if (this.changeCount > 0) {
      jQuery(this.lastForm).change();
    }

    popup.close();
  }

  return false;
}

OrderPaymentMethodDataView.prototype.handleClosePopup = function(event, data)
{
  if (this.getBox().length) {
    jQuery('.inline-field.inline-payment-method-data').get(0).endEdit();
    jQuery(data.box).dialog('destroy');
  }
}

OrderPaymentMethodDataView.prototype.setValues = function(i, field)
{
  var origBox = jQuery('form .payment-method-data-orig-values');
  if (0 < jQuery(origBox).length) {
    jQuery(origBox).find('input[type="hidden"]').each(function(){
      var fromName = this.name.replace(/orig-/, '');
      if ('payment[' + fromName + ']' == field.name) {
         jQuery(field).val(jQuery(this).val());
         field.commonController.saveValue();
      }
    });
  }
}

OrderPaymentMethodDataView.prototype.syncField = function(i, field)
{
  if (this.isElementChanged(field)) {
    var $field = jQuery(field);
    var transPrefix = 'transaction-' + jQuery('form input[name="transaction_id"]').val() + '-';
    var fieldName = field.name.replace(/payment\[(.+)\]/, "$1");
    var fieldPattern = '.' + transPrefix + fieldName.replace(/_/g, '-') + '-value :input';

    if (0 < jQuery(fieldPattern).length) {

      this.changeCount++;

      var from = $field; // Field in the popup form
      var to = jQuery(fieldPattern).eq(0); // Field on the main page
      // Text presentation of the field value (in view template)
      var placeholder = jQuery('.inline-payment-method-data .' + transPrefix + fieldName + '.payment-data-field-view .param-value').eq(0);

      this.syncFieldValue(from, to, placeholder);
    }
  }
}

OrderPaymentMethodDataView.prototype.isElementChanged = function(field)
{
  return field.commonController.isChanged();
}

OrderPaymentMethodDataView.prototype.syncFieldValue = function(from, to, placeholder)
{
  // Save value
  to.val(from.val());
  this.lastForm = to.get(0).form;

  // Save HTML
  if (placeholder.length > 0) {
    if (from.is('select')) {
      placeholder.html(from.val());

    } else {
      placeholder.html(from.val());
    }
  }
}

OrderPaymentMethodDataView.prototype.getBox = function()
{
  return jQuery('.ui-dialog .order-payment-data-dialog').eq(0);
}

new OrderPaymentMethodDataView();

jQuery(document).ready(function() {

  jQuery('.method select#paymentmethod').each(function() {
    jQuery(this).change(function() {
      var box = jQuery(this).parents('.method').eq(0);
      var paymentMethodData = jQuery(box).find('.payment-method-data').get(0);

      if (this.commonController.isChanged()) {
        jQuery(paymentMethodData).hide();
      } else {
        jQuery(paymentMethodData).show();
      }
    });
  });

});
