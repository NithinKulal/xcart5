/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Date picker controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
function datePickerPostprocess(input, elm)
{
}

jQuery().ready(
  function() {
    jQuery('.date-picker-widget').each(
      function () {
        var elem = jQuery(this);
        var options = core.getCommentedData(elem);

        jQuery(elem).find('input').datepicker({
          dateFormat:        options.dateFormat,
          gotoCurrent:       true,
          yearRange:         options.highYear + ':' + options.lowYear,
          showButtonPanel:   false,
          beforeShow:        datePickerPostprocess,
          selectOtherMonths: true
        });
    });
  }
);
