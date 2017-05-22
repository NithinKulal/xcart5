/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Migrate controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('.s3-migrate .bar').each(
      function () {
        this.errorState = false;
        jQuery(this).css('width', jQuery(this).data('percent') + '%');
      }
    ).bind(
      'error',
      function() {
        this.errorState = true;
      }
    ).bind(
      'complete',
      function() {
        if (!this.errorState) {
          self.location.reload();
        }
      }
    );
  }
);
