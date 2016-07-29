/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Create shipment button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function CapostButtonCreateShipment()
{
  var obj = this;

  jQuery('.capost-button-create-shipment').each(function () {
    
    var btn = this;
    var o = jQuery(this);

    var parcelId = core.getCommentedData(o, 'parcel_id');

    o.click(function (event) {
  
      popup.openAsWait();

      submitForm(btn.form, obj.getParams(parcelId));
    });
  });
}

CapostButtonCreateShipment.prototype.getParams = function (parcelId)
{
  var result = {
    'action': 'capost_create_shipment',
    'parcel_id': parcelId
  };

  return result;
};

core.autoload(CapostButtonCreateShipment);

