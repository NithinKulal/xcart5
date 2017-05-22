/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details override controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(function () {
   // Form AJAX-based submit

  jQuery(this).on('click', 'form.product-details [type="submit"]', function () {
    var form = jQuery('form.product-details');
    var files = form.find('.customer-attachments-values input[type=file]').get(0).files;

    if (0 < files.length) {
      var formData = new FormData();
      for (var i = 0; i < files.length; i++) {
        var file = files[i];

        // Add the file to the request.
        formData.append('customer_attachments[]', file, file.name);
      }

      var xhr = new XMLHttpRequest();

      xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
          var response = jQuery.parseJSON(xhr.responseText);
          var ids = response.ids;

          for (var i = 0; i < ids.length; i++) {
              form.append('<input type="hidden" name="attachments_ids[]" value="' + ids[i] + '">');
          }

          var msg = response.msg;
          jQuery.each(msg, function(key, value) {
              core.trigger('message', {'type':value.type, 'message':value.text});
          });

          form.submit();
        }
      };

      xhr.open('POST', URLHandler.buildURL({target: 'customer_attachments', action: 'ajax_upload'}), true);
      xhr.send(formData);

    } else {
      form.submit();
    }

    return false;
  });
});
