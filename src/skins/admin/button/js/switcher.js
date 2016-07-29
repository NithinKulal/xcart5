/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Remove button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('button.switcher').click(
      function () {
        var inp = jQuery(this).prev();
        var enable = !inp.attr('value');
        inp.attr('value', enable ? '1' : '');
        var btn = jQuery(this);
        if (enable) {
          btn.addClass('on').removeClass('off').attr('title', btn.data('lbl-disable'));

        } else {
          btn.addClass('off').removeClass('on').attr('title', btn.data('lbl-enable'));
        }
      }
    );
  }
);

