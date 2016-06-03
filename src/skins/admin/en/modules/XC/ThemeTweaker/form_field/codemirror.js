/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(
  function() {
    jQuery('textarea.codemirror').each(function () {
      var self = this;
      var element = jQuery(self);
      var mode = jQuery(this).data('codemirrorMode');

      var width = element.outerWidth();
      var height = element.outerHeight();

      var editor = CodeMirror.fromTextArea(
        self,
        {
          mode: mode,
          lineNumbers : true,
          viewportMargin: Infinity
        }
      );
      editor.setSize(width, height);

      editor.on('change', function (editor) {
        jQuery(self).text(editor.doc.getValue()).trigger('change');
      });

    });
  }
);
