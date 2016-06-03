/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sticky panel controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function StickyPanelProductSelection(base)
{
  base = jQuery(base);
  if (0 < base.length && base.hasClass('sticky-panel')) {
    base.get(0).controller = this;
    this.base = base;

    this.process();
  }
}

extend(StickyPanelProductSelection, StickyPanelModelList);

// Autoloader
StickyPanelProductSelection.autoload = function()
{
  jQuery('.sticky-panel.product-selection-sticky-panel').each(
    function() {
      new StickyPanelProductSelection(this);
    }
  );
};

// Reposition
StickyPanelProductSelection.prototype.reposition = function(isResize)
{

};
