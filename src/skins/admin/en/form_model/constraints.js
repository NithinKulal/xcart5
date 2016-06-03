/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Constraints
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

$(function () {

  Vue.validator('NotBlank', function (value, rule) {
    return !!value && 0 !== value.length;
  });

  Vue.validator('DateRange', function (value, rule) {
    var model = rule.model;
    value = this.vm.$get(model);

    return (!rule.min || value > rule.min) && (!rule.max || value < rule.max);
  });

});
