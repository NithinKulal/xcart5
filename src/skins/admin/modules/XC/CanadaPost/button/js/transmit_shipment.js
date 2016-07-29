/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Transmit shipment button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function CapostButtonTransmitShipment()
{
  var obj = this;

  jQuery('.capost-button-transmit-shipment').each(function () {
    
    var btn = this;
    var o = jQuery(this);

    var parcelId = core.getCommentedData(o, 'parcel_id');
    var warningText = core.getCommentedData(o, 'warning_text');

    o.click(function (event) {

      result = confirm(warningText);

      if (result) {
      
        popup.openAsWait();

        submitForm(btn.form, obj.getParams(parcelId));
      }
    });
  });
}

CapostButtonTransmitShipment.prototype.getParams = function (parcelId)
{
  var result = {
    'action': 'capost_transmit_shipment',
    'parcel_id': parcelId
  };

  return result;
};

core.autoload(CapostButtonTransmitShipment);

