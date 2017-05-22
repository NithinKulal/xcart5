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
      var el = this.el;
      var $el = $(el);
      var vm = this.vm;
      var model = this.expression;
      var format = this.params.format;
      var defaultDate = $el.val();

      $el.datepicker({
        dateFormat: format,
        defaultDate: defaultDate,
        onSelect: function (date) {
          vm.$set(model, '' + $(this).datepicker('getDate'));

          // DateRange validator is detached from change/blur field event
          // so trigger validation
          vm.$validate(true);
        }
      });

      $el.change(function(){
        vm.$set(model, '' + $(this).datepicker('getDate'));

        // DateRange validator is detached from change/blur field event
        // so trigger validation
        vm.$validate(true);
      });

      $el.blur(function () {
        var result = null;
        try {
          result = $.datepicker.parseDate(format, $el.val());
        } catch(err) {
          result = false;
        }

        if (!result) {
          $el.datepicker('setDate', defaultDate);
          $el.datepicker('refresh');
        }
      });

      $el.datepicker('setDate', defaultDate);
      $el.datepicker('refresh');
    }
  });

})();
