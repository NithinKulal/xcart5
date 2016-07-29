/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

decorate(
  'ProductsListView',
  'postprocess',
  function(isSuccess, initial)
  {
    arguments.callee.previousMethod.apply(this, arguments);

    if (isSuccess) {
      jQuery('.quicklook button.quicklook-link', this.base).click(
        function () {
          popup.openAsWait();

          return !popup.load(
            URLHandler.buildURL({
              target:      'quick_look',
              action:      '',
              product_id:  core.getValueFromClass(this, 'quicklook-link'),
              only_center: 1
            }),
            function () {
              jQuery('.formError').hide();
            },
            50000
          );
        }
      );

      core.bind('block.product.details.postprocess', function() {
         $('.cycle-slideshow').cycle();
      });
    }
  }
);
