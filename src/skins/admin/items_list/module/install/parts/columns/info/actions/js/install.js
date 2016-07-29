/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Modules list controller (install)
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(function () {
  core.microhandlers.add(
    'install-module-action',
    '.install-module-action',
    function () {
    var $this = jQuery(this);
      $this.click(function () {
        jQuery('.sticky-panel button, .sticky-panel .actions').trigger(
          'select-to-install-module',
          {
            id: $this.attr('data'),
            checked: $this.prop('checked'),
            moduleName: jQuery('.module-name', $this.closest('.module-main-section')).html()
          }
        );
      });
    }
  );
});
