/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Order history box
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function OrderEventDetails(base)
{
  this.callSupermethod('constructor', arguments);
}

extend(OrderEventDetails, ALoadable);

OrderEventDetails.prototype.base = '.order-history';

OrderEventDetails.autoload = function()
{
  new OrderEventDetails(jQuery('.order-history'));
}

OrderEventDetails.prototype.postprocess = function(isSuccess, initial)
{
  ALoadable.prototype.postprocess.apply(this, arguments);

  if (isSuccess && initial) {
    jQuery('.order-info .title-box .history a').click(_.bind(this.handleSwitch, this));
  }

  if (isSuccess) {
    this.base.find('.action i').click(
      function () {
        var elm = jQuery(this);
        if (elm.hasClass('fa-plus-square-o')) {
          elm.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');

        } else {
          elm.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
        }
      }
    );
  }
}

OrderEventDetails.prototype.handleSwitch = function(event)
{
  if (this.base.hasClass('in')) {
    this.base.collapse('hide');
    jQuery('.order-info .title-box .history a').html(core.t('View order history'));

  } else {
    this.base.collapse('show');
    jQuery('.order-info .title-box .history a').html(core.t('Hide order history'));
  }

  return false;
}

core.autoload('OrderEventDetails');
