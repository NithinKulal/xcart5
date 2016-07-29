/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Images settings page js controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(function() {
   jQuery(".preview").each(function() {
     var img = jQuery(this).data('img');
     if (img) {
       jQuery(this).tooltip({
        title : '<img src="' + xliteConfig.base_url  + 'skins/admin/images/' + img + '" />',
        html: true,
        placement: 'bottom',
        delay: { show: 100, hide: 400 },
       });
     }
   });

   jQuery('.sticky-panel.images-settings-panel .button-tooltip button').click(function(event) {

     var submitButton = jQuery('.sticky-panel.images-settings-panel button.submit').get(0);
     var proceed = true;

     if (!jQuery(submitButton).prop('disabled')) {
       proceed = confirm(core.t('There are unsaved changes on the page. If you choose to continue, these changes will be lost. Do you want to proceed?'));
     }

     if (proceed) {
       self.location = jQuery(this).data('url');

     } else {
       event.stopImmediatePropagation();
     }
   });
});
