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

BannerRotationController.prototype.fixHeight = function () {
  jQuery('#banner-rotation-widget .carousel-inner').height('auto');
  var firstItem = this.base.find('.item.active').first();

  var maxHeight = firstItem.find('img').height();
  if (maxHeight > 0) {
    firstItem.find('img').onload = function () {
      jQuery('#banner-rotation-widget .carousel-inner').height(maxHeight);
    };

    jQuery('#banner-rotation-widget .carousel-inner').height(maxHeight);
  }
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

