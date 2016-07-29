/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Skrill settings widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function() {
    jQuery('.mb-activation .mb-email button').click(
      function() {
        this.form.elements.namedItem('action').value = 'checkEmail';
        
        return true;
      }
    );
  }
);

