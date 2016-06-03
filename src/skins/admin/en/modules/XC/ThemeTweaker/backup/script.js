/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function() {
    var codeBackup = jQuery('#code_backup');
    codeBackup.show();
    var editorBackup = CodeMirror.fromTextArea(document.getElementById('backup'), {readOnly: 'nocursor'});
    codeBackup.hide();

    jQuery('#code_backup_link a').click(
      function () {
        jQuery('#code_backup_link').hide();
        codeBackup.show();

        return false;
      }
    );

    jQuery('#code_backup a.hide-button').click(
      function () {
        jQuery('#code_backup_link').show();
        codeBackup.hide();

        return false;
      }
    );
  }
);
