/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Taxes controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function() {
    jQuery('.edit-sales-tax table.form button.switch-state')
      .removeAttr('onclick')
      .click(
      function() {
        var o = this;
        o.disabled = true;
        core.post(
          URLHandler.buildURL({target: 'sales_tax', action: 'switch'}),
          function(XMLHttpRequest, textStatus, data, valid) {
            o.disabled = false;
            if (valid) {
              var td = jQuery('.edit-sales-tax table.form td.button');
              if (td.hasClass('enabled')) {
                td.removeClass('enabled');
                td.addClass('disabled');

              } else {
                td.removeClass('disabled');
                td.addClass('enabled');
              }
            }
          }
        );

        return false;
      }
    );

    jQuery('#ignore-memberships').click(
      function() {
        jQuery('.edit-sales-tax').toggleClass('no-memberships', jQuery(this).is(':checked'));
      }
    );

    jQuery('#taxablebase').change(
      function() {
        if ('P' == jQuery(this).val()) {
          jQuery('.edit-sales-tax').removeClass('no-taxbase');

        } else {
          jQuery('.edit-sales-tax').addClass('no-taxbase');
        }
      }
    );

    jQuery('#items-list-switcher').click(
      function() {
        if (jQuery('#shipping-rates').hasClass('hidden')) {
          jQuery('#shipping-rates').removeClass('hidden');
          jQuery('i.fa', this).removeClass('fa-caret-right');
          jQuery('i.fa', this).addClass('fa-caret-down');
        } else {
          jQuery('#shipping-rates').addClass('hidden');
          jQuery('i.fa', this).removeClass('fa-caret-down');
          jQuery('i.fa', this).addClass('fa-caret-right');
        }
      }
    );
  }
);
