/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * address_form.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Checkout.define('Checkout.ShippingAddressForm', ['Checkout.AddressForm'], function(){
  Checkout.ShippingAddressForm = Checkout.AddressForm.extend({
    name: 'shipping-address-form',
  });
});