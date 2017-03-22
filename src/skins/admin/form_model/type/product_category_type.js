/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('xliteProductCategory', {
    params: ['searchingLbl', 'noResultsLbl', 'enterTermLbl'],
    twoWay: true,
    bind: function () {
      var self = this;
      var $el = $(this.el);
      var model = this.expression;
      var term = '';

      $el
        .select2(
          {
            debug: true,
            language: {
              noResults: function () {
                return term.length ? self.params.noResultsLbl : self.params.enterTermLbl;
              },
              searching: function () {
                return self.params.searchingLbl;
              }
            },
            escapeMarkup: function (markup) { return markup; },
            templateResult: function (repo) {
              var term = $('.select2-search__field', $el.parent()).val();
              if (repo.loading) return repo.text;

              return repo.text.replace(new RegExp('('+term+')([^/]*)$', 'i'), '<em>$1</em>$2');
            },
            matcher: function (params, match) {
              if (params.term == null || $.trim(params.term) === '') {
                return match;
              }

              var re = new RegExp('('+params.term+')([^/]*)$', 'i');
              if (re.test(match.text)) {
                return match;
              }

              return null;
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

      this.vm.$watch(this.expression, function (newValue, oldValue) {
        self.vm.$set('form.default.category_tree', newValue);
      });

      this.vm.$watch('form.default.category_tree', function (newValue, oldValue) {
        self.vm.$set(self.expression, newValue);
      });

      setTimeout(function () {
        $el.closest('.input-widget').find('span.help-block a').click(function () {
          self.vm.$set('form.default.category_widget_type', 'tree');
          jQuery.cookie('product_modify_categroy_widget', 'tree');
        });

        $('#form_default_category_tree').attr('name', '').closest('.input-widget').find('span.help-block a').click(function () {
          self.vm.$set('form.default.category_widget_type', 'search');
          jQuery.cookie('product_modify_categroy_widget', 'search');
        });
      }, 1000);
    },
    update: function (newValue, oldValue) {
      if (newValue.filter(function (value) { return value.length }).length === 0) {
        return;
      }

      var $el = $(this.el);

      $el.val(newValue);
      $el.trigger('change');
    }
  });

})();
