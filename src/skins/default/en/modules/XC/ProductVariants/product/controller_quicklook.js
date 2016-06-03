/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product quicklook controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

window.ProductQuickLookVariantViewLoading = false;

/**
 * Controller
 */
function ProductQuickLookVariantView (base) {
  this.base = jQuery(base);
  var self = this;

  core.bind(
    'update-product-page',
    function(event, productId) {
      self.loadVariantsImages(productId);
    }
  );
}

ProductQuickLookVariantView.autoload = function()
{
  if (jQuery('.ui-dialog.ui-widget').length > 1) {
    jQuery('.ui-dialog.ui-widget:not(:last)').off();
    jQuery('.ui-dialog.ui-widget:not(:last)').remove();
  };

  jQuery('.widget-controller .product-quicklook .product-photo').each(
    function() {
      new ProductQuickLookVariantView(this);
    }
  );
};

// Load variant image
ProductQuickLookVariantView.prototype.loadVariantsImages = function(productId, shade)
{
  this.base = jQuery('.widget-controller .product-quicklook .product-photo');
  if (this.base.siblings('.has-variants-mark').length > 0
      && this.base.siblings('.has-variants-mark').data('value')
      && window.ProductQuickLookVariantViewLoading === false
  ) {
    window.ProductQuickLookVariantViewLoading = true;
    if (shade) {
      this.base.find('.image').append('<div class="single-progress-mark"><div></div></div>')
    }

    this.loadVariantsImagesShade = shade;

    // Request variant images info
    core.get(
      URLHandler.buildURL(this.getURLParametersForLoadVariantsImages(productId)),
      _.bind(this.handleLoadVariantsImages, this),
      null,
      { dataType: 'json' }
    );
  }
}

// Get URL parameters for variant image loading routine
ProductQuickLookVariantView.prototype.getURLParametersForLoadVariantsImages = function(productId)
{
  var params = {product_id: productId};
  params = array_merge(params, core.getWidgetsParams('update-product-page', params));

  return array_merge({'target': 'product', 'action': 'get_variant_images'}, params);
}

// Load variant image handler
ProductQuickLookVariantView.prototype.handleLoadVariantsImages = function (XMLHttpRequest, textStatus, data)
{
  if (data && _.isString(data)) {
    data = jQuery.parseJSON(data);
  }

  this.processVariantImageAsImage(data);

  if (this.loadVariantsImagesShade) {
    this.base.find('.product-details-info .single-progress-mark').remove();
  }

  window.ProductQuickLookVariantViewLoading = false;
}

ProductQuickLookVariantView.prototype.processVariantImageAsImage = function(data)
{
  if (data && _.isObject(data)) {
    var elm = this.base.find('img.product-thumbnail');
    elm.attr('width', data.main[0])
      .attr('height', data.main[1])
      .attr('src', data.main[2])
      .attr('alt', data.main[3])
      .css({ width: data.main[0] + 'px', height: data.main[1] + 'px' });

    this.base.find('a.cloud-zoom').attr('href', data.full[2]);
    this.base.find('.cloud-zoom').trigger('cloud-zoom');

  } else {
    this.applyDefaultImage();
  }

}

ProductQuickLookVariantView.prototype.applyDefaultImage = function()
{
  var img = this.base.siblings('.default-image').find('img');
  var elm = this.base.find('img.product-thumbnail');

  elm.attr('src', img.attr('src'))
    .attr('width', img.attr('width'))
    .attr('height', img.attr('height'))
    .css({
      'width':  img.attr('width') + 'px',
      'height': img.attr('height') + 'px'
    });

  this.base.find('a.cloud-zoom').attr('href', img.attr('src'));

  this.base.find('.loupe').hide();

  var zoom = this.base.find('a.cloud-zoom').data('zoom');
  if (zoom) {
    zoom.destroy();
  }
  this.base.find('a.cloud-zoom')
    .unbind('click')
    .click(function() { return false; });
}


core.autoload(ProductQuickLookVariantView);

core.microhandlers.add(
  'ProductQuickLookVariantView',
  '.widget-controller .product-quicklook .product-photo',
  function (event) {
    ProductQuickLookVariantView.autoload();
  }
);
