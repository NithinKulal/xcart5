/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Authy resend SMS controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


jQuery(function () {
  jQuery(this).on('click', '.top_messages_resend_token', function () {
    jQuery('.resend_token').eq(0).click();
    return false;
  });
});

/**
 * Controller
 */
function AuthyResendController (base) {
    AuthyResendController.superclass.constructor.apply(this, arguments);

    jQuery('.resend_token').click(_.bind(this.handleResendToken, this));
    core.bind('popup.close', _.bind(this.handleClosePopup, this));
}

extend(AuthyResendController, ALoadable);

AuthyResendController.autoload = function () {
  jQuery('.resend_token').each(function () {
    new AuthyResendController(this);
  });
};

AuthyResendController.prototype.handleResendToken = function (event) {
  popup.shade();

  core.post(
    URLHandler.buildURL({target: 'authy_login', action: 'resend_token'}),
    function (xhr, status, data, valid) {
        popup.unshade();
    },
    {}
  );

  return false;
};

AuthyResendController.prototype.handleClosePopup = function (event) {
  jQuery('.submit').removeAttr('disabled');
};


core.bind('afterPopupPlace', function () {
  new AuthyResendController();
});
