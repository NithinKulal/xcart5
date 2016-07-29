/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shopping cart controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
jQuery(document).ready(
  function() {
    jQuery('.selected-products form input[name="amount"]').change(
      function() {
        var result = true;
        var amount = parseInt(this.value);
        result = !isNaN(amount) && 0 < amount;

        if (result) {
          var btn = jQuery('.update-icon', jQuery(this).parents('.item-sums').eq(0));
          if (amount == this.initialValue) {
            btn.addClass('update-icon-disabled').prop('disabled', 'disabled');

          } else {
            btn.removeClass('update-icon-disabled').removeProp('disabled');
          }

        } else {
          this.value = this.initialValue;
        }

        return result;
      }
    )
    .each(
      function() {
        this.initialValue = this.value;
      }
    );
  }
);
