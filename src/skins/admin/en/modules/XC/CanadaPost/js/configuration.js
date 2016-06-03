/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Canada Post configuration
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function ($, window, undefined) {

  $(function () {
    
    $('#quote-type').bind('change', function () {

      $('#contract-id').closest('li')
        .add($('#pick-up-type').closest('li').prev().prev())
        .add($('#pick-up-type').closest('li'))
        .add($('#deposit-site-num').closest('li'))
        .add($('#detailed-manifests').closest('li'))
        .add($('#manifest-name').closest('li'))
        .toggle('C' == $(this).val());

    }).trigger('change');

    $('#pick-up-type').bind('change', function () {

      // Hide / display "Site number of the deposit location" field
      $('#deposit-site-num').closest('li').toggle('M' == $(this).val());

    }).trigger('change');

    $('#use-insurance').bind('change', function () {
      
      $('#insurance').closest('li')
        .toggle($(this).is(':checked'));

    }).trigger('change');

  });

})(jQuery, window);
