/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipments page controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function ($, window, undefined) {

  $(function () {

    $('.ca-package').each(function () {

      var obj = $(this);

      var parcelStatus = core.getCommentedData(obj, 'status');

      if (parcelStatus != 'P') {
        // Block options
        $('select,input,textarea', obj).prop('disabled', 'disabled');
      }

    });

    var base = $('.shipment-info .shipment-info-part');

    $('a.tracking-details-link', base).click(
      function () {
        return !popup.load(
          this,
          null,
          false
        );
      }
    );

  });

})(jQuery, window);
