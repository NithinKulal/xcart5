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
      var elem = this;
      var input = $("input.bootstrap-select-value", elem);
      var button = $('.input-group-btn button', elem);
      var options = $(".input-group-btn .dropdown-menu a", elem);
      var mainLabel = $('span.main-label', button);

      options.click(function (e) {
        e.preventDefault();

        input.val($(this).data('value')).change();

        button.focus();
      }).mousedown(function (e) {
        e.preventDefault();
      });

      input.change(function () {
        var val = $(this).val();

        options.each(function () {
          if ($(this).data('value') === val) {
            mainLabel.text($(this).text());
          }
        })
      });
    }
  }
);

