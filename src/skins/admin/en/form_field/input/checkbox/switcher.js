/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Switcher controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.line .input-field-wrapper.switcher:not(.switcher-read-only)',
    handler: function () {
      var input = jQuery(':checkbox', this);
      var cnt = jQuery(this);
      var widget = jQuery('.widget', this);

      widget.click(
        function () {
          if (!input.prop('disabled')) {
            input.click();

            input.change();
          }
        }
      );

      input.change(
        function () {
          if (this.checked) {
            cnt.addClass('enabled').removeClass('disabled');
            widget.attr('title', widget.data('enabled-label'));

          } else {
            cnt.removeClass('enabled').addClass('disabled');
            widget.attr('title', widget.data('disabled-label'));
          }
        }
      );
    }
  }
);
