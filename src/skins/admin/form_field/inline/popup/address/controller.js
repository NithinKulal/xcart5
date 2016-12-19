/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Order address popup
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-popup.inline-address',
    handler: function () {

      var field = jQuery(this);

      this.getPopupURL = function()
      {
        var url = field.data('popup-url');

        jQuery('.order-info .inline-address :input').each(
          function(idx, elm) {
            url += (-1 == url.search(/\?/) ? '?' : '&') + elm.name + '=' + encodeURIComponent(elm.value);
          }
        );

        return url;
      }

    }
  }
);

function OrderAddressView()
{
  OrderAddressView.superclass.constructor.apply(this, arguments);

  core.bind('afterPopupPlace', _.bind(this.handlePopupOpen, this));
}

extend(OrderAddressView, Base);

OrderAddressView.prototype.lastForm = null;

OrderAddressView.prototype.changeCount = 0;

OrderAddressView.prototype.openBook = false;

OrderAddressView.prototype.countryChanged = null;

OrderAddressView.prototype.handlePopupOpen = function()
{
  var box = this.getBox();

  if (box.length > 0) {
    this.openBook = false;

    box.find('form')
      .submit(_.bind(this.handleFormSubmit, this))
      .bind('beforeSubmit', function(event) { event.result = false; });
    box.find('form :input').filter(':visible').eq(0).focus();

    box.find('.expander a, .same-note a')
      .click(_.bind(this.handleExpand, this));
    box.find('.collapser a')
      .click(_.bind(this.handleCollapse, this));

    core.bind('popup.close', _.bind(this.handleClosePopup, this));

    box.find('a.address-book').each(
      function() {
        var elm = jQuery(this);
        if (elm.attr('onclick')) {
          var m = elm.attr('onclick').toString().match(/\.location[ ]*=[ ]*['"](.+)['"]/);
          elm.data('location', m[1]);
          elm.removeAttr('onclick');
        }
      }
    );

    box.find('a.address-book').click(_.bind(this.openAddressBook, this));
  }
}

OrderAddressView.prototype.handleFormSubmit = function(event)
{
  var form = this.getBox().find('form').get(0);
  if (form.commonController.validate()) {
    this.changeCount = 0;
    this.countryChanged = {
      shipping: false,
      billing:  false
    };
    this.getBox().find('form :input').each(_.bind(this.syncField, this));
    this.getBox().find('form .profile-login :input').each(_.bind(this.syncLoginField, this));
    if (this.changeCount > 0) {
      jQuery(this.lastForm).change();
    }

    popup.close();
  }

  return false;
}

OrderAddressView.prototype.handleClosePopup = function(event, data)
{
  if (this.getBox().length && !this.openBook) {
    jQuery('.inline-field.profile-billingAddress').get(0).endEdit();
    if (jQuery('.inline-field.profile-shippingAddress').get(0)) {
      jQuery('.inline-field.profile-shippingAddress').get(0).endEdit();
    }

    jQuery(data.box).dialog('destroy');

  } else if (this.openBook) {
    jQuery('.inline-field.profile-billingAddress').get(0).endEdit();
    if (jQuery('.inline-field.profile-shippingAddress').get(0)) {
      jQuery('.inline-field.profile-shippingAddress').get(0).endEdit();
    }
  }
}

OrderAddressView.prototype.handleExpand = function(event)
{
  jQuery(event.target)
   .parents('.address-box')
    .eq(0)
    .removeClass('collapsed');

  return false;
}

OrderAddressView.prototype.handleCollapse = function(event)
{
  jQuery(event.target)
    .parents('.address-box')
    .eq(0)
    .addClass('collapsed');

  return false;
}

OrderAddressView.prototype.syncField = function(i, field)
{
  if (this.isElementChanged(field)) {
    var $field = jQuery(field);
    var parts = field.name.split('_');
    var prefix = parts.shift();
    var name = parts.join('-');
    var fieldPattern = '.' + prefix + 'address-' + name + '-value :input';

    if (0 < jQuery(fieldPattern).length) {

      this.changeCount++;

      var sync = true;
      var from = $field;
      var to = jQuery(fieldPattern).eq(0);
      var placeholder = jQuery('.profile-' + prefix + 'Address .address-' + parts.join('_') + ' .address-field').eq(0);

      if (-1 != field.name.search(/_custom_state/)) {
        if ($field.is(':visible')) {
          this.syncFieldValue(
            from,
            to,
            jQuery('.profile-' + prefix + 'Address .address-state .address-field').eq(0)
          );
          jQuery('.profile-' + prefix + 'Address .address-state_id .address-field').html('');

        } else {
          from.val('');
        }

      } else if (-1 != field.name.search(/_country_code/)) {
        this.countryChanged[prefix] = true;

      } else if (-1 != field.name.search(/_state_id/)) {
        jQuery('.profile-' + prefix + 'Address .address-custom_state .address-field').html('');
        jQuery('.profile-' + prefix + 'Address .address-state').hide();
      }

      if (sync) {
        this.syncFieldValue(from, to, placeholder);
      }

    }
  }
}

OrderAddressView.prototype.syncLoginField = function(i, field)
{
  if (this.isElementChanged(field)) {
    var $field = jQuery(field);
    var parts = field.name.split('_');
    var prefix = parts.shift();
    var name = parts.join('-');
    var fieldPattern = 'input[name="'+name+'"]';

    if (0 < jQuery(fieldPattern).length) {
      this.changeCount++;

      var from = $field;
      var to = jQuery(fieldPattern).eq(0);
      var placeholder = jQuery(fieldPattern).closest('.inline-field').find('.view').eq(0);

      to.val(from.val());
      this.lastForm = to.get(0).form;

      placeholder.html(from.val());
    }
  }
}

OrderAddressView.prototype.isElementChanged = function(field)
{
  var result = field.commonController.isChanged();

  if (!result && -1 != field.name.search(/_state_id|_custom_state/) && jQuery(field).is(':visible')) {
    var parts = field.name.split('_');
    var prefix = parts.shift();
    result = this.countryChanged[prefix];
  }

  return result;
}

OrderAddressView.prototype.syncFieldValue = function(from, to, placeholder)
{
  // Save value
  to.val(from.val());
  this.lastForm = to.get(0).form;

  // Save HTML
  if (placeholder.length > 0) {
    if (from.is('select')) {
      placeholder.html(from.get(0).options[from.get(0).selectedIndex].text);

    } else {
      placeholder.html(from.val());
    }
  }

  // Erase address id
  var name = to.parents('.inline-field').hasClass('profile-shippingAddress')
    ? 'shippingAddress'
    : 'billingAddress';
  to.parents('.field').eq(0).find('input[name="' + name + '[id]"]').val(0);

  // Unlock shipping address
  if (to.parents('.inline-field').hasClass('profile-shippingAddress')) {
    to.parents('.inline-field').removeClass('same-as-billing');
    to.parents('form').find('input[name="shippingAddress[same_as_billing]"]').val('');
  }
}

OrderAddressView.prototype.getBox = function()
{
  return jQuery('.ui-dialog .order-address-dialog').eq(0);
}

OrderAddressView.prototype.openAddressBook = function(event)
{
  this.openBook = true;

  var elm = jQuery(event.currentTarget).eq(0);

  if (elm.attr('disabled') != 'disabled') {
    elm.attr('disabled', 'disabled');
    popup.load(elm);
  }

  return false;
}

core.autoload(OrderAddressView);
