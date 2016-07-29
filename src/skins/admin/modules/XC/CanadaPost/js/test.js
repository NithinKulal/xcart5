/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Canada Post test
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function ($, window, undefined) {

  $(function () {
    
    $('#destination-country').bind('change', function () {

      $('#destination-postal-code').closest('li')
        .toggle('US' == $(this).val() || 'CA' == $(this).val());

    }).trigger('change');

    $.validationEngineLanguage.allRules.canadianPostalCode = {
      "regex": /^[ABCEGHJKLMNPRSTVXY]\d[A-Z] ?\d[A-Z]\d$/,
      "alertText": "* Invalid Canadian Post Code"
    };

  });

})(jQuery, window);
