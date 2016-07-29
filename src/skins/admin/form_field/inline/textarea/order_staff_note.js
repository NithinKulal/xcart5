/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Order notes field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-order-staff-note',
    handler: function () {

      var field = jQuery(this);

      this.nl2br = function(str) {
        return (str + '').replace(/(?:\r\n|\n\r|\r|\n)/g, '<br />');
      }

      // Function to update view part of widget
      this.updateMoreLessLinksVisibility = function() {

        var content = jQuery('.view .value', this).html().trim();

        // Set view value
        jQuery('.view .value', this).html(this.nl2br(content));

        // Prepare truncated content
        var maxCount = field.data('max-length');
        var maxRowLength = field.data('max-row-length');

        var truncated = '';
        var displayMoreLink = false;

        content = content.slice(0, maxCount);
        var rows = content.split(/(?:\r\n|\r|\n)/);
        var count = 0;
        for (i = 0; i < rows.length; i++) {
          var rowCharsCount = maxRowLength * Math.ceil(rows[i].length / maxRowLength);
          truncated = truncated + rows[i].slice(0, rowCharsCount);
          count = count + rowCharsCount;
          if (count < maxCount) {
            truncated = truncated + "\n";
          } else {
            displayMoreLink = true;
            truncated = truncated.trim() + '...';
            break;
          }
        }

        // Set trunated view value
        jQuery('.view .truncated', this).html(this.nl2br(truncated.trim()));

        if (field.hasClass('in-progress')) {
          field.removeClass('in-progress');
        }

        if (displayMoreLink) {
          jQuery('.view', this).addClass('has-more-link');

        } else {
          jQuery('.view', this).removeClass('has-more-link');
        }
      }

      this.updateMoreLessLinksVisibility();

      // Process Ctrl+Enter
      field.find('.field textarea').keydown(
        _.bind(
          function (e) {
            if (e.ctrlKey && e.keyCode == 13) {
              // Ctrl-Enter pressed
              this.endEdit();
            }
          },
          this
        )
      );

      // Process more/less link hover
      field.find('.more-less-link').hover(
        function() {
          field.addClass('expand-focused');
        },
        function() {
          field.removeClass('expand-focused');
        }
      );

      // Process more/less link click
      field.find('.view .more-less-link').click(
        _.bind(
          function(event) {
            if (field.hasClass('expanded')) {
              field.removeClass('expanded');

            } else {
              field.addClass('expanded');
            }

            return false;
          },
          this
        )
      );

      field.bind(
        'afterSaveFieldInline',
        function(event, data) {
          if (data.value) {
            field.removeClass('empty').addClass('filled');
          }
        }
      );

      field.bind(
        'saveEmptyFieldInline',
        function(event) {
          field.removeClass('filled').addClass('empty');
          this.getViewValueElements().html(field.find('.value').data('empty'));
          this.updateMoreLessLinksVisibility();
          field.removeClass('expanded');
        }
      );

      field.bind(
        'beforeSaveFieldInline',
        function(event, data) {
          data.value = $('<div>').html(data.value).text()
        }
      );

      field.bind(
        'afterSaveFieldInline',
        function(event) {
          this.updateMoreLessLinksVisibility();
          field.removeClass('expanded');
        }
      );

      field.bind(
        'beforeStartEditInline',
        function(event) {
          this.lastWidth = jQuery('.view', this).outerWidth();
        }
      );

      field.bind(
        'startEditInline',
        function(event) {
          var box = jQuery('.field', this);
          var w = box.outerWidth();
          box.css('margin-right', (this.lastWidth - w - 4) + 'px')
        }
      );
    }
  }
);
