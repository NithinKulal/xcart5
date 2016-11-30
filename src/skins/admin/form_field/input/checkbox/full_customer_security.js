/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function FullCustomerSecuritySwitcher () {
  jQuery('#force-customers-to-https').change(function (event)
  {
    var input = $(this);
    event.stopImmediatePropagation();

    input.attr('disabled', true);
    core.get(
      URLHandler.buildURL({
        target: 'https_settings',
        action: 'switch_customer_security'
      }),
      function(xhr, status, data) {
        if (status !== 'success') {
          input.attr('checked',!input.attr('checked'));
        }
        input.attr('disabled', false);
      }
    ).done(function (data) {
      if (false !== data['Success']) {
        console.log(data);
        core.trigger('message', {
          type: 'info',
          message: data['NewState'] ? core.t('Enabled') : core.t('Disabled')
        });
      }
    });

    return false;
  });
}

core.autoload(FullCustomerSecuritySwitcher);