/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Modules list controller (install)
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

ItemsList.prototype.listeners.popup = function(handler)
{
  // TODO: REWORK to load it dynamically with POPUP button widget JS files
  core.autoload(PopupButtonInstallAddon);
  core.autoload(PopupButtonSelectInstallationType);
};

jQuery(document).ready(
  function () {
    // Top filters
    jQuery('.combine-selector a.chosen-single').each(
      function() {
        var a =  jQuery(this);
        var label = a.parents('.combine-selector').eq(0).find('label').eq(0);
        a.children().eq(0).before('<strong>' + label.html() + '</strong>');
      }
    );

    jQuery('#addons-sort').bind('change', function(event) {
      location.replace(jQuery(this).val());
    });

    jQuery('#price-filter').bind('change', function(event) {
      location.replace(jQuery(this).val());
    });

    jQuery('#tag-filter').bind('change', function(event) {
      location.replace(jQuery(this).val());
    });

    jQuery('#vendor-filter').bind('change', function(event) {
      location.replace(jQuery(this).val());
    });

    ItemsListQueue();
  }
);
