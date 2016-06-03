/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sale widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function SalePriceValueBlock() {

  if (!jQuery('#participate-sale').is(':checked')) {
    jQuery('.sale-discount-types').hide();
  }

  // Binding "Change" functionality to participate-sale checkbox
  jQuery('#participate-sale').change(
    function () {
      if (this.checked) {
        jQuery('.sale-discount-types').show();

      } else {
        jQuery('.sale-discount-types').hide();
      }
    }
  );
}

core.autoload(SalePriceValueBlock);
