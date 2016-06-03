/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Change attribute-values additional controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind(
  'load',
  function() {
    decorate(
      'CartView',
      'postprocess',
      function(isSuccess, initial)
      {
        arguments.callee.previousMethod.apply(this, arguments);

        if (isSuccess) {

          jQuery('.item-change-attribute-values a', this.base).click(
            _.bind(
              function(event) {
                return !popup.load(event.currentTarget, _.bind(this.closePopupHandler, this));
              },
              this
            )
          );
        }
      }
    );
  }
);
