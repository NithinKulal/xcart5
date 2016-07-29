/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Delete address butoon controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ButtonDeleteAddress()
{
  var obj = this;

  jQuery('.delete-address').each(
    function () {
      var o = jQuery(this);
      var addressId = core.getCommentedData(o, 'address_id');
      var warningText = core.getCommentedData(o, 'warning_text');

      o.click(function (event) {
        result = confirm(warningText);
        if (result) {
          self.location = URLHandler.buildURL(obj.getParams(addressId));
        }
      });
    }
  );
}

ButtonDeleteAddress.prototype.getParams = function (addressId) 
{
  var params = {
    'address_id'  : addressId,
    'target'      : 'address_book',
    'action'      : 'delete'
  };
  params[xliteConfig.form_id_name] = xliteConfig.form_id;
  
  return params;
};

core.autoload(ButtonDeleteAddress);
