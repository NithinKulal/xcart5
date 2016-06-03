/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Total field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function validateCouponTotal(field, rules, i, options)
{
  var pattern = field.attr('name').replace(/Begin/, '%').replace(/End/, '%');
  var begin = -1 == field.attr('name').search(/Begin/)
    ? field.parents('form').find('[name="' + pattern.replace(/%/, 'Begin') + '"]').eq(0)
    : field;
  var end = -1 == field.attr('name').search(/End/)
    ? field.parents('form').find('[name="' + pattern.replace(/%/, 'End') + '"]').eq(0)
    : field;

  if (begin.length && end.length && begin.val() && end.val()) {
    var bd = parseFloat(begin.val());
    var ed = parseFloat(end.val());
    if (!isNaN(bd) && !isNaN(ed) && bd != 0 && ed != 0) {
      if (begin.get(0).name == field.get(0).name && bd > ed) {
        return lbl_validate_coupons_begin_total_error;

      } else if (end.get(0).name == field.get(0).name && bd > ed) {
        return lbl_validate_coupons_end_total_error;
      }
    }
  }
}

