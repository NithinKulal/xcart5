/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Bootstrap select common field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-bootstrap-select',
    handler: function () {
      var elem = this,
        input = $("input.bootstrap-select-value", elem),
        button = $('.input-group-btn button', elem),
        mainLabel = $('span.main-label', button);

      $(".input-group-btn .dropdown-menu a", elem).click(function (e) {
        e.preventDefault();

        input.val($(this).data('value')).change();
        mainLabel.text($(this).text());

        button.focus();
      }).mousedown(function (e) {
        e.preventDefault();
      });
    }
  }
);

