/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    canApply: function () {
      return this.$element.is('select[id|="methodid"]');
    },
    handler: function () {
      var count = 10;

      var handler = _.bind(
        function() {
          var widget = this.$element.next('.chosen-container');
          if (widget.length) {
            widget.find('.chosen-results li').each(
              _.bind(
                function(idx, elm) {
                  var oid = jQuery(elm).data('option-array-index');
                  var methodId = this.element.options[oid].value;
                  var html = jQuery('.shipping-rates-data li#shippingMethod' + methodId).html();
                  if (html) {
                    jQuery(elm).find('label span').html(html);
                  }
                },
                this
              )
            );

          } else if (count > 0) {
            count--;
            setTimeout(_.bind(arguments.callee, this), 500);
          }
        },
        this
      );

      handler();
    }
  }
);

