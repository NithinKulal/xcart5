/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

decorate(
  'ListView',
  'postprocess',
  function(isSuccess, initial)
  {
    arguments.callee.previousMethod.apply(this, arguments);

    if (isSuccess) {
      var o = this;

      jQuery('.per-page-box .list-type-grid a', this.base).click(function () {
        jQuery('html, body').animate({scrollTop: o.base.offset().top});
        return !o.load({'itemsPerPage': jQuery(this).data('items-per-page')});
      });
    }
  }
);
