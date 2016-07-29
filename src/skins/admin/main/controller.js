/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Admin Welcome block js-controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */
jQuery().ready(
  function() {

    // Close 'Welcome...' block
    jQuery('.admin-welcome .welcome-footer .close-button', this.base).click(
      function () {

        var wrapper = jQuery(this).closest('.admin-welcome');

        var ch = jQuery('.hide-welcome-block', wrapper);

        var action = ch.attr('name');

        if (ch.is(':checked')) {
          action += '_forever';
        }

        jQuery.ajax({
          url: xliteConfig.script + "?target=main&action=" + action
        }).done(function() { 
        });

        wrapper.hide();

        if (!jQuery('.admin-welcome:visible').length) {
          jQuery('.admin-welcome-indent').removeClass('admin-welcome-indent');
        }
      }
    );
  }
);
