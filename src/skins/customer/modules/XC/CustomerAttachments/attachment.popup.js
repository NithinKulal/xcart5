/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attachment popup
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


jQuery(document).ready(
  function() {
    var currentLink = null;

    core.microhandlers.add(
      'attachment-popup',
      'a.customer-attachment',
      function() {
        jQuery(this).click(
          function(event) {
            currentLink = event.currentTarget;

            loadDialogByLink(
              event.currentTarget,
              URLHandler.buildURL({
                'target':  'customer_attachments',
                'widget':  '\\XLite\\Module\\XC\\CustomerAttachments\\View\\AttachmentPopup',
                'popup':   1,
                'item_id': jQuery(this).siblings('input[name="item_id"]').val()
              }),
              {width: 'auto'},
              null,
              this
            );

            return false;
          }
        )
      }
    );

    core.bind('afterPopupPlace', function(){
      if (currentLink) {
        currentLink.linkedDialog = popup.currentPopup.box;

        var count = popup.currentPopup.box.find('li').length;
        if(count > 0) {
          jQuery(currentLink).siblings('.files-attached').find('.files-count').html(count);
        } else {
          jQuery(currentLink).siblings('.files-attached').remove();
        }
      }
    });

  }
);
