/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Ajax ShopGate API key generation controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var previouslySelectedMailChimpList = '';

core.bind(
  'load',
  function(event) {
    jQuery('[id^="radio-default-"]').each(
      function () {
        var elem = jQuery(this);

        if (elem.prop('checked')) {
          previouslySelectedMailChimpList = elem.val()
        }

        elem.click(
          function () {
            elem = jQuery(this);

            if (
              elem.prop('checked')
              && elem.val() == previouslySelectedMailChimpList
            ) {
              elem.prop('checked', false);
              previouslySelectedMailChimpList = '';
            } else {
              previouslySelectedMailChimpList = elem.val();
            }
          }
        );
      }
    );
  }
);
