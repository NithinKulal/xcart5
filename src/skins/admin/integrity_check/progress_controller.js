/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Calculate quick data controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
jQuery().ready(
    function() {
      jQuery('.integrity-check-progress .bar')
          .bind(
              'error',
              function() {
                this.errorState = true;
                self.location = URLHandler.buildURL({ 'target': 'integrity_check', 'process_failed': 1 });
              }
          )
          .bind(
              'complete',
              function() {
                if (!this.errorState) {
                  self.location = URLHandler.buildURL({ 'target': 'integrity_check', 'process_completed': 1 });
                }
              }
          );
    }
);
