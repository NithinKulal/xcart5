/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Customer attachments list script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

if ('undefined' !== typeof(OrderInfoForm)) {
  var old = OrderInfoForm.prototype.isElementAffectRecalculate;
  OrderInfoForm.prototype.isElementAffectRecalculate = function (element) {
    var result = old.apply(this, arguments);

    if (result && -1 != element.name.search(/^delete_attachment/)) {
      result = false;
    }

    return result;
  };
}

jQuery(function () {
  jQuery('.attachments-input-wrapper').click(function () {
    var $this = jQuery(this);

    $this.find('input').click();
    if ($this.find('input').is(':checked')) {
      $this.addClass('to_delete')
        .siblings('a').addClass('to_delete');
    } else {
      $this.removeClass('to_delete')
        .siblings('a').removeClass('to_delete');
    }
  });
});
