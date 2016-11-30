/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Layout type controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function LayoutType(base) {
  var o = this;

  o.base = base;
  o.base.commonController = o;

  o.selector = jQuery('.hidden-field select', o.base);

  jQuery('.layout-type', this.base).bind('click', _.bind(o.handleClickLayoutType, o));
  o.selector.bind('change', _.bind(o.handleChange, o));
}

LayoutType.prototype.base = null;
LayoutType.prototype.selector = null;

LayoutType.prototype.handleChange = function (event, data) {
  var o = this;
  var preview = jQuery('.layout-settings .preview img');

  assignWaitOverlay(o.base);
  if (o.base.hasClass('layout-group-default')) {
    assignShadeOverlay(preview);
  }

  core.get(
    URLHandler.buildURL({
      target: 'layout',
      action: 'change_layout',
      layout_type: data.layoutType,
      layout_group: data.layoutGroup,
    }),
    function () {
      preview.attr('src', data.layoutPreview);
      unassignWaitOverlay(o.base);
      if (o.base.hasClass('layout-group-default')) {
        unassignShadeOverlay(preview);
      }
    }
  );
};

LayoutType.prototype.handleClickLayoutType = function (event) {
  jQuery('.layout-type', this.base).removeClass('selected');
  var selected = jQuery(event.currentTarget).addClass('selected');
  this.setLayoutType(selected.data());
};

LayoutType.prototype.setLayoutType = function (data) {
  this.selector.val(data.layoutType);
  this.selector.trigger('change', data);
};

core.autoload(LayoutType, '.layout-types');
