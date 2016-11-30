/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Constraints
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('form_model/constraints', ['vue/vue', 'js/jquery'], function (Vue, $) {

  Vue.validator('Backend', function (value, rule) {
    return true;
  });

  Vue.validator('NotBlank', function (value, rule) {
    return !!$.trim(value) && 0 !== value.length;
  });

  Vue.validator('MetaDescription', function (value, rule) {
    var notBlank = !!$.trim(value) && 0 !== value.length;
    var isCustom = this._vm.$get(rule.dependency) === rule.dependency_value;
    return !isCustom || notBlank;
  });

  Vue.validator('MaxLength', {
    check: function (value, rule) {
      return value.length <= rule.length;
    }
  });

  Vue.validator('DateRange', function (value, rule) {
    var model = rule.model;
    value = this.vm.$get(model);

    return (!rule.min || value > rule.min) && (!rule.max || value < rule.max);
  });

});
