/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Template selector controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function TemplatesSelector() {
  var o = this;

  o.base = jQuery('.templates:first');
  o.base.commonController = o;

  o.selector = jQuery('.hidden-field select', o.base);

  jQuery('.template', o.base).bind('click', _.bind(o.handleClickTemplate, o));
  jQuery('.template.marked', o.base).addClass('active');

  jQuery('button.submit', o.base.closest('form')).bind('click', function (e) {
    if (jQuery('.template.checked', o.base).data('is-redeploy-required') == 1
      && !confirm(core.t('To make your changes visible in the customer area, cache rebuild is required. It will take several seconds. You don’t need to close the storefront, the operation is executed in the background.'))
    ) {
      e.stopPropagation();
      e.preventDefault();

      return false;
    }
  });
}

TemplatesSelector.prototype.base = null;
TemplatesSelector.prototype.selector = null;

TemplatesSelector.prototype.handleClickTemplate = function (event) {
  jQuery('.template', this.base).removeClass('checked');
  this.setTemplate(jQuery(event.currentTarget).addClass('checked').data('template-id'));
  var settingsWidget = jQuery('.layout-settings.settings');
  if (this.selector.parents('form').get(0).isChanged()) {
    assignShadeOverlay(settingsWidget);
  } else {
    unassignShadeOverlay(settingsWidget);
  }
};

TemplatesSelector.prototype.setTemplate = function (template) {
  this.selector.val(template);
  this.selector.trigger('change');
};

core.autoload(TemplatesSelector);
