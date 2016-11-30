/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Import / export controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function() {
    jQuery('.bulk-edit-progress .bar')
      .bind(
        'error',
        function() {
          this.errorState = true;
          var scenario = $(this).closest('form').get(0).elements['scenario'].value;
          self.location = URLHandler.buildURL({target:'bulk_edit',scenario:scenario,failed:1});
        }
      )
      .bind(
        'complete',
        function() {
          if (!this.errorState) {
            var scenario = $(this).closest('form').get(0).elements['scenario'].value;
            self.location = URLHandler.buildURL({target:'bulk_edit',scenario:scenario,completed:1});
          }
        }
      );
  }
);
