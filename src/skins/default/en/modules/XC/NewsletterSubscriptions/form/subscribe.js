/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * SubscribeBlock controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function SubscribeBlockView(base)
{
  var args = Array.prototype.slice.call(arguments, 0);
  this.callSupermethod('constructor', args);

  // Form
  var form = this.base.find('.subscription-form-block form').get(0);
  if (form) {
    form.commonController.enableBackgroundSubmit(
      undefined,
      _.bind(
        function(event, result){
          if (result.isValid && result.textStatus === 'success') {
            this.processSuccess();
          } else {
            this.processError();
          }
        },
        this
      )
    );
  }
}

extend(SubscribeBlockView, ALoadable);

SubscribeBlockView.autoload = function()
{
  jQuery('.subscription-block').each(
    function() {
      new SubscribeBlockView(this);
    }
  );
};

// Widget target
SubscribeBlockView.prototype.widgetTarget = 'newsletter_subscriptions';

// Widget class name
SubscribeBlockView.prototype.widgetClass = '\\XLite\\Module\\XC\\NewsletterSubscriptions\\View\\SubscribeBlock';

SubscribeBlockView.prototype.processSuccess = function()
{
  this.base.find('.subscription-form-block').hide();

  this.base.find('.subscription-error-block').hide();

  this.base.find('.subscription-success-block').removeClass('hidden');
  this.base.find('.subscription-success-block').show();
}

SubscribeBlockView.prototype.processError = function()
{
  this.base.find('.subscription-error-block').removeClass('hidden');
  this.base.find('.subscription-error-block').show();
}

// Get event namespace (prefix)
SubscribeBlockView.prototype.getEventNamespace = function()
{
  return 'NewsletterSubscriptions';
}

core.autoload(SubscribeBlockView);
