/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Pick address from address book controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
core.trigger(
  'load',
  function() {
    var form = jQuery('form.select-address').eq(0);
    jQuery('.select-address .addresses > li').click(
      function() {
        form.get(0).elements.namedItem('addressId').value = core.getValueFromClass(this, 'address')
        form.submit();
      }
    );
  }
);
