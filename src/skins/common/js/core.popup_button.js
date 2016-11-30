/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Popup open button
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var lastPopupButton;

function PopupButton(base)
{
  var obj = this;

  if (base) {
    this.base = base;
    this.eachCallback(base);
  } else {
    this.base = jQuery(this.pattern);
    this.base.each(
      function () {
        obj.eachCallback(this);
      }
    );
  }
}

PopupButton.prototype.pattern = '.popup-button';

PopupButton.prototype.enableBackgroundSubmit = true;

PopupButton.prototype.options = {
  width: 'auto'
};

PopupButton.prototype.afterSubmit = function (selector) {
};

PopupButton.prototype.callback = function (selector, link)
{
  var obj = this;

  var shouldClose = jQuery(this.base).data('without-close') === true || !jQuery(this.base).data('without-close');

  if (!this.enableBackgroundSubmit) {
    jQuery('form', selector).each(
        function () {
          this.commonController.backgroundSubmit = false;
        }
    );
  } else if (shouldClose) {
    jQuery('form', selector).each(
      function() {
        jQuery(this).commonController(
          'enableBackgroundSubmit',
          function () {
            // Close dialog (but it is available in DOM)
            popup.close();
            openWaitBar();

            return true;
          },
          function (event) {
            closeWaitBar();

            obj.afterSubmit(selector);

            // Remove dialog from DOM
            jQuery(selector).remove();
            link.linkedDialog = null;

            return false;
          }
        );
      }
    );

  }

  core.autoload(PopupButton);
};

PopupButton.prototype.getURLParams = function (button)
{
  return core.getCommentedData(button, 'url_params');
};

PopupButton.prototype.getJSConfirmText = function (button)
{
  return core.getCommentedData(button, 'jsConfirm');
};

PopupButton.prototype.eachClick = function (elem)
{
  lastPopupButton = jQuery(elem);

  var proceed = true;

  if (lastPopupButton.hasClass('disabled')) {
    return false;
  }

  if (this.getJSConfirmText(elem)) {
    proceed = confirm(this.getJSConfirmText(elem));
  }

  if (proceed) {
    this.beforeLoadDialog(elem);
    return loadDialogByLink(
      elem,
      URLHandler.buildURL(this.getURLParams(elem)),
      this.options,
      _.bind(this.callback, this),
      elem
    );
  }

  return proceed;
};

PopupButton.prototype.beforeLoadDialog = function(elem) {};

PopupButton.prototype.eachCallback = function (elem)
{
  var obj = this;
  elem.popupController = obj;

  var handler = _.bind(obj.eachClick, this);
  jQuery(elem).click(
    function(event) {
      handler(this);
      event.stopImmediatePropagation();

      return false;
    }
  );
};
