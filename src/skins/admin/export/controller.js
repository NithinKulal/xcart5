/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Import / export controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function() {
    jQuery('.export-progress .bar')
      .bind(
        'changePercent',
        function(event, data) {
          if (data && 'undefined' != typeof(data.timeLabel)) {
            jQuery('.export-progress .time').html(data.timeLabel);
          }
        }
      )
      .bind(
        'error',
        function() {
          this.errorState = true;
          self.location = URLHandler.buildURL({ 'target': 'export', 'failed': 1 });
        }
      )
      .bind(
        'complete',
        function() {
          if (!this.errorState) {
            self.location = URLHandler.buildURL({ 'target': 'export', 'completed': 1 });
          }
        }
      );

    var height = 0;
    jQuery('.export-completed .files.std ul li.file').each(
      function () {
        height += jQuery(this).outerHeight();
      }
    );

    var bracket = jQuery('.export-completed .files ul li.sum .bracket');
    var diff = bracket.outerHeight() - bracket.innerHeight();

    bracket.height(height - diff);

    jQuery('.sections input').change(
      function() {
        if (jQuery('.sections input:checked').not(':disabled').length) {
          jQuery('.export-box .submit').removeClass('disabled');

        } else {
          jQuery('.export-box .submit').addClass('disabled');
        }

        if (jQuery('.sections input#sectionXLiteLogicExportStepProducts:checked').not(':disabled').length) {
          jQuery('.options .attrs-option').show();

        } else {
          jQuery('.options .attrs-option').hide();
        }

      }
    );

    jQuery('.export-box.export-begin form').submit(
      function() {
        if (!jQuery('.sections input:checked').not(':disabled').length) {
          return false;
        }
      }
    );
  }
);
