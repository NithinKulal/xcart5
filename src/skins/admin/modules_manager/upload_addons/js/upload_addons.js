/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Upload addons controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonUploadAddon()
{
  PopupButtonUploadAddon.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonUploadAddon, PopupButton);

PopupButtonUploadAddon.prototype.pattern = '.upload-addons';

PopupButtonUploadAddon.prototype.enableBackgroundSubmit = false;

core.autoload(PopupButtonUploadAddon);

core.microhandlers.add(
  'PopupButtonUploadAddon',
  PopupButtonUploadAddon.prototype.pattern,
  function (event) {
    core.autoload(PopupButtonUploadAddon);
  }
);

window.core.multiAdd = function (addArea, addObj, removeElement)
{
  var cloneObj;

  if (cloneObj == undefined) {
    cloneObj = {};
  }

  jQuery(addObj).click(
    function ()
    {
      if (cloneObj[addArea] == undefined) {
        cloneObj[addArea] = jQuery(addArea);
      }

      cloneObj[addArea].clone().append(
        jQuery(removeElement).click(
          function()
          {
            jQuery(this).closest(addArea).remove();
          }
        )
      )
      .insertAfter(cloneObj[addArea]);
    }
  );
}
