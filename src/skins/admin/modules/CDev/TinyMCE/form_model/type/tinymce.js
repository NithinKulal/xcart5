/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('tinymce', {
    twoWay: true,

    bind: function () {
      var self = this;
      var model = self.expression;

      jQuery(this.el).live('change', function () {
        var text = $(this).text();
        setTimeout(function () {
          self.vm.$set(model, text);
        }, 0);
      });
    }
  });

})();