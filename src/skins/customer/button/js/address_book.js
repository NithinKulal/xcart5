/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add address button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var PopupButtonAddressBook = PopupButton.extend({
  constructor: function PopupButtonAddressBook() {
    PopupButtonAddressBook.superclass.constructor.apply(this, arguments);
  },

  pattern: '.address-book-button',

  callback: function () {
    PopupButtonAddressBook.superclass.callback.apply(this, arguments);
    
    core.autoload(PopupButtonAddAddress);

    var form = jQuery('form.select-address').eq(0);
    jQuery(form).commonController(
      'enableBackgroundSubmit',
      _.bind(this.onBeforeSubmit, this),
      _.bind(this.onAfterSubmit, this)
    );
    jQuery('.select-address .addresses > li').click(
      function() {
        form.get(0).elements.namedItem('addressId').value = $(this).data('address-id');
        form.submit();
      }
    );
  },

  onBeforeSubmit: function() {},

  onAfterSubmit: function() {
    popup.destroy();
  }
});