/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Header search box
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'header-search',
  '.header-search',
  function() {
    var base = jQuery(this);

    base.find('.dropdown-menu li a').click(
      function () {
        var a = jQuery(this);
        base.find('input[name="substring"]').attr('placeholder', a.data('placeholder'));
        base.find('input[name="code"]').val(a.data('code'));
        base.find('input[name="substring"]')
          .click()
          .focus();

        return false;
      }
    );
  }
);

