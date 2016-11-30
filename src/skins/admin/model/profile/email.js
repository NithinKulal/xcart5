/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Email input handler
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    pattern: 'input#login',
    canApply: function () {
      return this.$element.is('#login') && 0 < this.$element.closest('li.input').find('.login-comment').length;
    },
    handler: function () {
      var comment = this.$element.closest('li.input').find('.login-comment');

      this.$element.bind('keyup', _.bind(function () {
        comment.toggle(this.$element.val() !== this.element.initialValue)
      }, this)).trigger('keyup');
    }
  }
);