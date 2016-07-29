/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('datepicker', {
    twoWay: true,
    params: ['format'],
    bind: function () {
      var $el = $(this.el);
      var vm = this.vm;
      var model = this.expression;

      $el.datepicker({
        dateFormat: this.params.format,
        onSelect: function (date) {
          vm.$set(model, '' + $(this).datepicker('getDate') / 1000);

          // DateRange validator is detached from change/blur field event
          // so trigger validation
          vm.$validate(true);
        }
      });
    },
    update: function (val) {
      if ('' !== val) {
        $(this.el).datepicker('setDate', new Date(val * 1000));
      }
    }
  });

})();
