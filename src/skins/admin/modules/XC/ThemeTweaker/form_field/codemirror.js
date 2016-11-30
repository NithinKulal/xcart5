/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function CodeMirrorWidget(base) {
  var element = base;
  var mode = element.data('codemirrorMode');

  var width = element.outerWidth();
  var height = element.outerHeight();

  var editor = CodeMirror.fromTextArea(
    element.get(0),
    {
      mode: mode,
      lineNumbers : true,
      viewportMargin: Infinity
    }
  );
  editor.setSize(width, height);

  editor.on('change', function (editor) {
    element.text(editor.doc.getValue()).val(editor.doc.getValue()).trigger('change');
  });
}

core.autoload(CodeMirrorWidget, 'textarea.codemirror.autoloadable');