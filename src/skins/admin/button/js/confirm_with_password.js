/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Confirm with password controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonConfirmWithPassword()
{
  PopupButtonConfirmWithPassword.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonConfirmWithPassword, PopupButton);

PopupButtonConfirmWithPassword.prototype.pattern = '.confirm-with-password';

decorate(
  'PopupButtonConfirmWithPassword',
  'callback',
  function (selector)
  {
    arguments.callee.previousMethod.apply(this, arguments);

    // var obj = this;

    jQuery('form', selector).each(
      function() {
        jQuery(this).commonController(
          'enableBackgroundSubmit'/*,
          undefined,
          function (event, xhr) {

            if (1 == xhr.data) {
              jQuery(obj.pattern).closest('form').submit();
            } else {
              obj.eachClick(jQuery(obj.pattern).get(0));
            }

            return false;
          }*/
        );
      }
    );

    // Some autoloading could be added
    jQuery('.cancel button').each(
      function () {
        jQuery(this).attr('onclick', '')
          .bind(
          'click',
          function (event) {
            event.stopPropagation();
            jQuery(selector).dialog('close');

            return true;
          });
      }
    );
  }
);

core.autoload(PopupButtonConfirmWithPassword);

core.bind(
  'passwordconfirmed',
  function(event, result) {
    var element = jQuery('.confirm-with-password');
    if (result.result) {
      element.closest('form').submit();
    } else {
      element.get(0).popupController.eachClick(element);
    }
  }
);
