/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('xliteSelect2', {
    params: ['searchingLbl', 'noResultsLbl'],
    twoWay: true,
    bind: function () {
      var self = this;
      var $el = $(this.el);
      var model = this.expression;

      $el
        .select2(
          {
            language: {
              noResults: function () {
                return self.params.noResultsLbl;
              },
              searching: function () {
                return self.params.searchingLbl;
              }
            },
            escapeMarkup: function (markup) {
              return markup;
            }
          }
        )
        .on('select2:select', _.bind(function (e) {
          var $el = $(this.el);
          this.vm.$set(model, $el.val() || []);
        }, this))
        .on('select2:unselect', _.bind(function (e) {
          var $el = $(this.el);
          this.vm.$set(model, $el.val() || []);
        }, this));
    }
  });

})();
