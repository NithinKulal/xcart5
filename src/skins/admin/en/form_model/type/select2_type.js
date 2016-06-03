/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('xliteSelect2', {
    twoWay: true,
    bind: function () {
      var $el = $(this.el);
      var model = this.expression;

      $el
        .select2(
          {}
        )
        .on('select2:select', _.bind(function (e) {
          var $el = $(this.el);
          this.vm.$set(model, $el.val() || []);
        }, this))
        .on('select2:unselect', _.bind(function (e) {
          var $el = $(this.el);
          this.vm.$set(model, $el.val() || []);
        }, this));
    },
    update: function (nv, ov) {
      var $el = $(this.el);

      $(this.el).val(nv);
      $el.trigger('change');
    }
  });

})();
