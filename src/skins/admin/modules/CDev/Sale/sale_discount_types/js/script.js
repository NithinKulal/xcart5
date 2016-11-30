/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sale widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function SalePriceValue() {

  jQuery('.discount-type input:radio:checked').closest('ul.sale-discount').addClass('active');

  jQuery('.discount-type input:radio').bind('click', function () {

    jQuery('ul.sale-discount').removeClass('active');

    jQuery(this).closest('ul.sale-discount').addClass('active');

    var input = jQuery('#sale-price-value-' + jQuery(this).val());

    input.focus();

    jQuery('input[name="postedData[salePriceValue]"]').val(input.val());
  });

  jQuery('.sale-discount .sale-price-value input[type="text"]').bind('change', function () {
    jQuery('input[name="postedData[salePriceValue]"]').val(jQuery(this).val());
  });
}

core.autoload(SalePriceValue);