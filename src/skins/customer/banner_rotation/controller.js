/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Banner rotation: customer zone controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function BannerRotationController(element) {
  this.base = jQuery(element);
  this.options = core.getCommentedData(element);
  this.startCarousel();
  this.markActive();
  this.fixHeight();
  this.assignHandlers();
}

BannerRotationController.prototype.base = null;
BannerRotationController.prototype.options = null;

BannerRotationController.prototype.startCarousel = function () {
  this.base.carousel(this.options);
  this.base.carousel('cycle');
};

BannerRotationController.prototype.markActive = function () {
  var firstItem = this.base.find('.item').first();
  firstItem.addClass('active');

  var firstIndicator = this.base.find('.carousel-indicators li').first();
  firstIndicator.addClass('active');
};

BannerRotationController.prototype.isImageOk = function (img) {
  if (!img.complete) {
      return false;
  }

  if (typeof img.naturalWidth !== "undefined" && img.naturalWidth === 0) {
      return false;
  }

  return true;
};

BannerRotationController.prototype.fixHeight = function () {
  var carouselInner = jQuery('#banner-rotation-widget .carousel-inner');

  var firstItem = this.base.find('.item').first();

  carouselInner.height('auto');
  var maxHeight = firstItem.find('img').height();

  if (maxHeight > 0 && this.isImageOk(firstItem.find('img').get(0))) {
    carouselInner.height(maxHeight);
  }

  firstItem.find('img').get(0).onload = function () {
    carouselInner.height('auto');
    carouselInner.height(firstItem.find('img').height());
  };
};

BannerRotationController.prototype.assignHandlers = function () {
  jQuery(window).resize(_.bind(this.fixHeight, this));

  // FIXME: Leaking abstraction, this code should be in decorator in [XC\ThemeTweaker] module.
  // 'layout.moved' is the event of ThemeTweaker layout editor.
  core.bind(
    'layout.moved',
    _.bind(function(event, args) {
      if(args.id === this.base.closest('.list-item').data('id')) {
        this.fixHeight();
      }
    }, this)
  );
}

core.microhandlers.add(
  'BannerRotation',
  '#banner-rotation-widget',
  function (event) {
    new BannerRotationController(this);
  }
);
