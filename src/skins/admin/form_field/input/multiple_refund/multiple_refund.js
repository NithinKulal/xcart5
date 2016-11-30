/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Multiple refund
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    pattern: '.multiple-refund > button',
    canApply: function () {
      return 0 < this.$element.filter('button.refund').length;
    },
    handler: function () {
      var input = this.$element.parents('.multiple-refund:first').find('input.refund-amount');
      this.$element.click(function () {
        if (_.isUndefined(input.get(0).commonController) || input.get(0).commonController.validate({silent: false, focus: true})) {
          if (confirm(core.t("Are you sure?"))) {
            var amount = input.val();
            self.location = core.getCommentedData(this, 'link') + '&amount=' + amount;
          }
        }
      });
    }
  }
);

CommonElement.prototype.handlers.push(
  {
    pattern: '.multiple-refund > input',
    canApply: function () {
      return 0 < this.$element.filter('input.refund-amount').length;
    },
    handler: function () {
      this.$element.focusin(function () {
        $(this).parents('.multiple-refund:first').find('button.refund').addClass('input-focus');
      }).focusout(function () {
        $(this).parents('.multiple-refund:first').find('button.refund').removeClass('input-focus');
      });

      this.$element.keypress(function (e) {
        if(e.which == 13) {
          $(this).parents('.multiple-refund:first').find('button.refund').click();
        }
      });

      this.$element.on("invalid", function (e) {
        $(this).parents('.multiple-refund:first').find('button.refund').addClass('validation-error');
      }).on("valid", function (e) {
        $(this).parents('.multiple-refund:first').find('button.refund').removeClass('validation-error');
      });
    }
  }
);