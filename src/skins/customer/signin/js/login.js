/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Login link
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(
  function() {
    var height = jQuery('.signin-login-wrapper').height() - 70;
    jQuery('.or-line .line').height(height);
    jQuery('.or-line .or-box').css('top', height/2 + 15);
   // jQuery('.signin-anonymous-box').css('width', jQuery('.signin-anonymous-box').width());

    // Login popup form
    core.microhandlers.add(
      'loginPopupForm',
      'form.login-form',
      function(event) {
        this.commonController.enableBackgroundSubmit();
        var form = this.commonController.$form;

        form.find('a.forgot').click(
          function(event) {
            loadDialogByLink(
              event.currentTarget,
              URLHandler.buildURL({
                'target': 'recover_password',
                'widget': '\\XLite\\View\\RecoverPassword',
                'popup': 1,
                'fromURL': (self.location + ''),
                'email': form.find('#login-email').val() ? form.find('#login-email').val() : ''
              }),
            {width: 'auto'},
            null,
              this
              );

            return false;
          }
        );

        var f = this.commonController.getErrorPlace;
        this.commonController.getErrorPlace = function()
        {
          if (!this.errorPlace) {
            var box = f.apply(this);
            box
              .remove()
              .insertBefore(this.$form.find('button[type="submit"]').eq(0));
          }
          return this.errorPlace;
        };

        core.bind(
          'recoverPasswordSent',
          function() {
            jQuery('.popup-window-entry').dialog('close');
          }
        );
      }
    );

    // Recovery password popup form
    core.microhandlers.add(
      'recoverPasswordPopupForm',
      'form.recovery-form',
      function(event) {
        this.commonController.enableBackgroundSubmit();
        var form = this.commonController.$form;
        jQuery('.back-login', form).remove();
      }
    );
  }
);

