/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Calculate quick data controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
jQuery().ready(
  function() {
    jQuery('.quick-data-progress .bar')
      .bind(
        'changePercent',
        function(event, data) {
          if (data && 'undefined' != typeof(data.timeLabel)) {
            jQuery('.quick-data-progress .time').html(data.timeLabel);
          }
        }
      )
      .bind(
        'error',
        function() {
          this.errorState = true;
          self.location = URLHandler.buildURL({ 'target': 'cache_management', 'resize_failed': 1 });
        }
      )
      .bind(
        'complete',
        function() {
          if (!this.errorState) {
            self.location = URLHandler.buildURL({ 'target': 'cache_management', 'resize_completed': 1 });
          }
        }
      );

    var height = 0;
    jQuery('.quick-data-completed .files.std ul li.file').each(
      function () {
        height += jQuery(this).outerHeight();
      }
    );

    var bracket = jQuery('.quick-data-completed .files ul li.sum .bracket');
    var diff = bracket.outerHeight() - bracket.innerHeight();

    bracket.height(height - diff);
  }
);
