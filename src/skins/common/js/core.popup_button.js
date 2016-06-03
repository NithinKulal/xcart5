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
    this.eachCallback(base);
  } else {
    jQuery(this.pattern).each(
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

  if (this.enableBackgroundSubmit) {
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

  } else {
    jQuery('form', selector).each(
      function() {
        this.commonController.backgroundSubmit = false;
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
    return loadDialogByLink(
      elem,
      URLHandler.buildURL(this.getURLParams(elem)),
      this.options,
      this.callback,
      this
    );
  }

  return proceed;
};

PopupButton.prototype.eachCallback = function (elem)
{
  var obj = this;
  elem.popupController = obj;

  jQuery(elem).click(
    function(event) {
      obj.eachClick(this);
      event.stopImmediatePropagation();

      return false;
    }
  );
};
