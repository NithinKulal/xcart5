/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Controller
 */

core.bind(
  'update-product-page',
  function (event, productId)
  {
    jQuery('div.product-details').get(0).controller.block.loadVariantsImages(productId);
  }
);

jQuery(window).ready(
  function()
  {
    core.bind(
      'update-product-page',
      function (event, productId)
      {
        jQuery('div.product-details').get(0).controller.block.loadVariantsImages(productId);
      }
    );


    var use = jQuery('div.product-details').data('use-widgets-collection');
    var productId = jQuery('input[name="product_id"]', form).val();
    if ('undefined' != typeof(use) && !use) {
      core.bind(
        'update-variant-image',
        function (event, productId)
        {
          jQuery('div.product-details').get(0).controller.block.loadVariantsImages(productId, true);
        }
      );
      var form = jQuery("ul.attribute-values").closest('form');
      var handler = function () {
        core.trigger('update-variant-image', productId);
      };
      
      jQuery("ul.attribute-values input[type='checkbox']").unbind('change').change(handler);
      jQuery("ul.attribute-values select").unbind('change').change(handler);
    }
    jQuery('div.product-details').get(0).controller.block.loadVariantsImages(productId);
  }
);

ProductDetailsView.prototype.fakeLoupeLink = null;

// Load variant image
ProductDetailsView.prototype.loadVariantsImages = function(productId, shade)
{
  if (this.base.data('variants-has-images')) {

    if (shade) {
      this.base.find('.product-details-info').append('<div class="single-progress-mark"><div></div></div>')
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
ProductDetailsView.prototype.getURLParametersForLoadVariantsImages = function(productId)
{
  var params = {product_id: productId};
  params = array_merge(params, core.getWidgetsParams('update-product-page', params));

  return array_merge({'target': 'product', 'action': 'get_variant_images'}, params);
}

// Load variant image handler
ProductDetailsView.prototype.handleLoadVariantsImages = function (XMLHttpRequest, textStatus, data)
{
  if (data && _.isString(data)) {
    data = jQuery.parseJSON(data);
  }

  if (this.base.find('.product-image-gallery:visible').length > 0) {
    this.processVariantImageAsGallery(data);

  } else {
    this.processVariantImageAsImage(data);
  }

  if (this.loadVariantsImagesShade) {
    this.base.find('.product-details-info .single-progress-mark').remove();
  }
}

ProductDetailsView.prototype.processVariantImageAsGallery = function(data)
{

  var imageChanged = false;

  var variantImageSelected = this.base.find('.product-image-gallery li.variant-image.selected').length > 0;

  if (data && _.isObject(data)) {

    imageChanged = this.base.find('.product-image-gallery li.variant-image a').attr('href') != data.full[2];

    if (imageChanged) {

      // Remove old variant image
      var li = this.base.find('.product-image-gallery li:eq(0)').clone(true);

      this.base.find('.product-image-gallery li.variant-image').remove();
      
      // Change images
      var elm = li.find('a img');
      elm.attr('width', data.gallery[0])
        .attr('height', data.gallery[1])
        .attr('src', data.gallery[2])
        .attr('alt', data.gallery[3])
        .css({ width: data.gallery[0] + 'px', height: data.gallery[1] + 'px' });

      elm = li.find('img.middle');
      elm.attr('width', data.main[0])
        .attr('height', data.main[1])
        .attr('src', data.main[2])
        .attr('alt', data.main[3])
        .css({ width: data.main[0] + 'px', height: data.main[1] + 'px' });

      // Change gallery link
      li.find('a')
        .attr('href', data.full[2])
        .attr('rev', 'width: ' + data.full[0] + ', height: ' + data.full[1]);

      li.addClass('variant-image');

      this.base.find('.product-image-gallery li:eq(0)').before(li);

      // Gallery icon vertical aligment
      var margin = (li.height() - li.find('a img').height()) / 2;

      li.find('a img').css({
        'margin-top':    Math.ceil(margin) + 'px',
        'margin-bottom': Math.floor(margin) + 'px'
      });
    }

  } else if (this.base.find('.product-image-gallery li.variant-image').length > 0) {

    imageChanged = true;

    // Remove old variant image
    this.base.find('.product-image-gallery li.variant-image').remove();

  }

  if (imageChanged) {
    // Gallery reinitialization
    this.gallery = jQuery('.product-image-gallery li', this.base);
    this.hideLightbox();

    this.base.find('.product-image-gallery li:eq(0) a').click();
  }

}

ProductDetailsView.prototype.processVariantImageAsImage = function(data)
{
  if (!this.fakeLoupeLink) {
    this.fakeLoupeLink = this.base.find('.default-image');
    this.fakeLoupeLink.colorbox(this.getColorboxOptions());
    this.base.find('.loupe')
      .unbind('click')
      .click(
        _.bind(
          function(event) {
            this.fakeLoupeLink.click();
          },
          this
        )
      );
  }

  if (data && _.isObject(data)) {
    var elm = this.base.find('img.product-thumbnail');
    elm.attr('width', data.main[0])
      .attr('height', data.main[1])
      .attr('src', data.main[2])
      .attr('alt', data.main[3])
      .css({ width: data.main[0] + 'px', height: data.main[1] + 'px' });

    this.fakeLoupeLink.attr('href', data.full[2]);
    this.base.find('.loupe').show();

    this.base.find('a.cloud-zoom').attr('href', data.full[2]);
    this.base.find('.cloud-zoom').trigger('cloud-zoom');

  } else {
    this.applyDefaultImage();
  }

}

ProductDetailsView.prototype.applyDefaultImage = function()
{
  if (this.base.find('.product-image-gallery li').length > 0) {
    var a = this.base.find('.product-image-gallery li a');
    var img = this.base.find('.product-image-gallery li img.middle');
    var elm = this.base.find('img.product-thumbnail');
    elm.attr('src', img.attr('src'))
      .attr('width', img.attr('width'))
      .attr('height', img.attr('height'))
      .css({
        'width':  img.attr('width') + 'px',
        'height': img.attr('height') + 'px'
      });

    this.fakeLoupeLink.attr('href', a.attr('href'));
    this.base.find('.loupe').show();

    this.base.find('a.cloud-zoom').attr('href', a.attr('href'));
    this.base.find('.cloud-zoom').trigger('cloud-zoom');

  } else {
    var img = this.base.find('.default-image img');
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
}
