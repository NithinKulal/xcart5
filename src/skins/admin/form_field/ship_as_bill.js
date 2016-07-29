/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipping-as-billing address checkbox controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ShipAsBillHandler()
{
  this.flag = jQuery('#ship-as-bill');
  this.block = jQuery('.shipping-section');

  // Event handlers
  var o = this;

  this.flag.click(
    function(event) {
      return o.changeFieldsAccessability();
    }
  );

  this.changeFieldsAccessability();
}

ShipAsBillHandler.prototype.flag = null;
ShipAsBillHandler.prototype.block = null;

ShipAsBillHandler.prototype.changeFieldsAccessability = function()
{
  this.block.find('input, select, textarea').prop('disabled', this.flag.prop('checked') ? 'disabled' : '');
  this.flag.prop('disabled', '');
};

jQuery(document).ready(
  function(event) {
    var shipAsBillHandler = new ShipAsBillHandler();
  }
);
