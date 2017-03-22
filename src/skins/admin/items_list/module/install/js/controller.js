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


var RequestForUpgrade = Object.extend({
  base: null,
  button: null,
  moduleId: null,

  constructor: function RequestForUpgradeConstructor() {
    this.base = jQuery('.request-for-upgrade');
    this.moduleId = this.base.data('module-id');
    this.button = this.base.find('button');

    this.bindListeners();
  },

  bindListeners: function() {
    this.button.click(_.bind(this.sendRequest, this));
  },

  sendRequest: function () {
    var url = {
      target: 'addons_list_marketplace',
      action: 'request_for_upgrade'
    };
    var data = {
      'module': this.moduleId
    };

    data[window.xliteConfig.form_id_name] = window.xliteConfig.form_id;

    core.post(
        url,
        _.bind(this.success, this),
        data
    );
  },

  success: function (XMLHttpRequest, textStatus, data, isValid) {
    var self = this;

    if (isValid) {
      this.button.get(0).progressState.setStateSuccess();

      _.delay(function() {
        self.button.fadeOut();
      }, 1000);

    } else {
      this.button.get(0).progressState.setStateFail();

      _.delay(function() {
        self.button.get(0).progressState.setStateStill();
      }, 2000);
    }
  },
});

core.autoload(RequestForUpgrade);
