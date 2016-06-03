/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Delete user button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonDeleteUser()
{
  PopupButtonDeleteUser.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonDeleteUser, PopupButton);

PopupButtonDeleteUser.prototype.pattern = '.delete-user-button';

decorate(
  'PopupButtonDeleteUser',
  'callback',
  function (selector)
  {
    // Some autoloading could be added
    jQuery('.button-cancel').each(
      function () {

        jQuery(this).attr('onclick', '')
        .bind(
          'click',
          function (event) {
            event.stopPropagation();

            jQuery(selector).dialog('close');

            return true;
          });

      });
  }
);

core.autoload(PopupButtonDeleteUser);
