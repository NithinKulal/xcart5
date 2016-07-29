/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Storefront status js
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var StorefrontStatusView = function(base)
{
  base = base || jQuery('#header .storefront-status');

  Base.apply(this, [base]);

  this.base = base;
  this.blocked = false;

  this.initialize();
}

extend(StorefrontStatusView, Base);

StorefrontStatusView.prototype.initialize = function()
{
  this.base.find('a.toggler').click(
    _.bind(
      function(event) {
        var result = true;
        if (!this.blocked && this.userConfirm()) {
          this.blocked = true;
          jQuery(this.base).addClass('disabled');
          result = core.get(jQuery(event.currentTarget).attr('href'));
          if (result) {
            this.switchState();
          }
        }

        return !result;
      },
      this
    )
  );

  core.bind('switchstorefront', _.bind(this.handleSwicthStorefront, this));
}

StorefrontStatusView.prototype.userConfirm = function()
{
  var toggler = this.base.find('.toggler');
  var result = true;

  if (toggler.hasClass('on')) {
    result = confirm(core.t('Do you really want to close storefront?'));
  }

  return result;
}

StorefrontStatusView.prototype.handleSwicthStorefront = function(event,data)
{
  var toggler = this.base.find('.toggler');

  if (
    (data.opened && toggler.hasClass('off'))
    || (!data.opened && toggler.hasClass('on'))
  ) {
    this.switchState();
  }

  if (data.link) {
    toggler.attr('href', data.link);
  }

  if (data.privatelink) {
    this.base.find('.link.closed a').attr('href', data.privatelink);
  }

  jQuery(this.base).removeClass('disabled');

  this.blocked = false;
}

StorefrontStatusView.prototype.switchState = function()
{
  var toggler = this.base.find('.toggler');

  if (toggler.hasClass('off')) {
    toggler.removeClass('off').addClass('on');
    this.base.removeClass('closed').addClass('opened');

  } else {
    toggler.removeClass('on').addClass('off');
    this.base.removeClass('opened').addClass('closed');
  }
}

core.autoload('StorefrontStatusView');
