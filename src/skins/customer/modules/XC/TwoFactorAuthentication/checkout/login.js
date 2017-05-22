/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Call popup on checkout login
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(function () {
  jQuery('form.login-form').bind('afterSubmit', function (event, data) {
    if (data.isValid) {
      loadDialogByLink(
        jQuery(this)[0],
        URLHandler.buildURL({
          'target':  'authy_login',
          'preReturnURL': URLHandler.buildURL({target: 'checkout'}),
          'widget':  '\\XLite\\Module\\XC\\TwoFactorAuthentication\\View\\CustomerLogin',
          'popup':   1
        }),
        {width: 'auto'},
        null,
        this
      );
    }
  });

  jQuery(document).on('click', '.resend_token', function(){
    popup.shade();
    core.post(
      URLHandler.buildURL({target: 'authy_login', action: 'resend_token'}),
      function(xhr, status, data, valid) {
          popup.unshade();
      },
      {}
    );

    return false;
  });
});
