/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Password (visible) controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    canApply: function () {
      return 0 < this.$element.filter('input[type="password"].password-visible').length;
    },
    handler: function () {
      var handler = function(event)
      {
        var p = this.$element.parent();
        var $element = this.$element;

        if (!this.$element.textInputElement) {
          // Initialize the text input element
          this.$element.textInputElement = this.$element.clone();
          this.$element.textInputElement
            .attr('class', 'password-hidden form-control')
            .attr('type', 'text')
            .removeAttr('id')
            .removeAttr('name')
            .keyup(function (event) {
              $element.val(jQuery(this).val());
              $element.trigger('keyup');
            });
          $element.keyup(function (event) {
            $element.textInputElement.val(jQuery(this).val());
          });
          p.prepend(this.$element.textInputElement);
        }

        if (jQuery(event.currentTarget).hasClass('close')) {
          // Text input must be shown (hide password)
          $element.textInputElement.removeClass('password-hidden');
          $element.addClass('password-hidden');
          p.nextAll('.eye').eq(0).addClass('opened');
        } else {
          // Password input must be shown (hide text)
          $element.textInputElement.addClass('password-hidden');
          $element.removeClass('password-hidden');
          p.nextAll('.eye').eq(0).removeClass('opened');
        }

        return false;
      };

      this.$element.parent().nextAll('.eye').eq(0).find('.open,.close').click(_.bind(handler, this));
    }
  }
);
