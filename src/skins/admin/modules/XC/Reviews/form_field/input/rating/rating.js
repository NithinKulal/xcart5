/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Vote bar controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function VoteBarController(base)
{
  this.callSupermethod('constructor', arguments);

  var form = jQuery(this.base).closest('form').get(0);
  if (form) {
      this.productId = jQuery('input[name=product_id]', form).eq(0).val();
  }

  if (this.productId) {
    this.returnTarget = jQuery('input[name=return_target]', form).eq(0).val();
    this.block = new VoteBarView(this.base, this.productId, this.returnTarget);
  }
}

extend(VoteBarController, AController);

// Controller name
VoteBarController.prototype.name = 'VoteBarController';

// Find pattern
VoteBarController.prototype.findPattern = '.product-average-rating';

// Controller associated main widget
VoteBarController.prototype.block = null;

// Controller associated buttons block widget
VoteBarController.prototype.buttonsBlock = null;

// Initialize controller
VoteBarController.prototype.initialize = function()
{
  var o = this;

  this.base.bind(
    'reload',
    function(event, box) {
      event.stopImmediatePropagation();
      o.bind(box);
    }
  );
};

function VoteBarClick(obj) {

  if (!obj._rating) {
    obj._rating = parseInt(jQuery(obj).attr('class').match(/\d+/));
  }

  jQuery(obj).closest('.vote-bar.editable').find('input[name=rating]').val(obj._rating).change();

  // Set selected rating in current object properties
  mainObj = jQuery(obj).closest('.vote-bar.editable').get(0);
  mainObj._rating = obj._rating;

  return false;
}

/**
 * Main widget
 */
function VoteBarView(base, productId, returnTarget)
{
  this.callSupermethod('constructor', arguments);

  this.productId = productId;
  this.returnTarget = returnTarget;

  this.linkClickHandler = function(event)
  {
    event.stopPropagation();

    return false;
  };
}

extend(VoteBarView, ALoadable);

// Product id
VoteBarView.prototype.productId = null;

// Return target (product page or product reviews page)
VoteBarView.prototype.returnTarget = null;

// Rating
VoteBarView.prototype.rating = null;

// Shade widget
VoteBarView.prototype.shadeWidget = true;

// Widget target
VoteBarView.prototype.widgetTarget = 'review';

// Widget class name
VoteBarView.prototype.widgetClass = '\\XLite\\Module\\XC\\Reviews\\View\\AverageRating';

// Previous value of 'action' input
VoteBarView.prototype.prevFormActionValue = '';
VoteBarView.prototype.prevFormActionInput = null;
VoteBarView.prototype.prevShadeWidget = null;

// Postprocess widget
VoteBarView.prototype.postprocess = function(isSuccess, initial)
{
  this.callSupermethod('postprocess', arguments);

  if (isSuccess) {

    var o = this;

    var widgetClass = jQuery('input[name=target_widget]', o.base).eq(0).val();
    if (widgetClass) {
      this.widgetClass = widgetClass;
    }

    jQuery(this.base).find('.vote-bar.editable .stars-row.hovered .star-single').bind(
      'click',
      function(event)
      {
        VoteBarClick(this);

        return o.rateProduct(event, jQuery(this));
      }
    );

    jQuery(this.base).bind('re-load', function (event) {
      o.load();
    });

    this.base.closest('form').eq(0).commonController('bindElements');
  }
};

// Get additional parameters
VoteBarView.prototype.getParams = function(params)
{
  params = this.callSupermethod('getParams', arguments);
  params.product_id = this.productId;

  return params;
};

// Form submit handler
VoteBarView.prototype.rateProduct = function(event, block)
{
  var form = jQuery(block).closest('.product-average-rating');

  var rating = jQuery('input[name=rating]', form).eq(0).val();
  this.rating = rating;

  var productId = jQuery(block).closest('form').find('input[name=product_id]').eq(0).val();
  if (productId) {
    this.productId = productId;
  }

  var block = jQuery(block).closest('form');

  core.post(
    URLHandler.buildURL(
      {
        target: 'review',
        action: 'rate'
      }
    ),
    function () {
      jQuery(VoteBarController.prototype.findPattern, block).trigger('re-load');
    },
    {
      'product_id': productId,
      'rating' : rating
    }
  );

  return false;
};

// Form POST processor
VoteBarView.prototype.postprocessRateProduct = function(XMLHttpRequest, textStatus, data, isValid)
{
};

core.autoload(VoteBarController);

// Required for ability to rate products after ajax reloading of products list
core.bind('list.products.postprocess', function() {
  core.autoload(VoteBarController);
});

core.bind('block.product.details.postprocess', function() {
  core.autoload(VoteBarController);
});

CommonForm.elementControllers.push(
  {
    pattern: '.vote-bar.editable .stars-row.hovered .star-single',
    handler: function () {
      jQuery(this)
        .hover(
          function() {
            if (!this._previous) {
              this._previous = jQuery(this).prevAll();
            }
            this._previous.each(function(index) {
                jQuery(this).addClass('over');
            });
            jQuery(this).addClass('over');

          }, function() {

              if (!this._previous) {
                this._previous = jQuery(this).prevAll();
              }
              this._previous.each(function(index) {
                  jQuery(this).removeClass('over');
              });
              jQuery(this).removeClass('over');
          })
          .click(function() {
            return VoteBarClick(this);
          });
    }
  }
);

CommonForm.elementControllers.push(
  {
    pattern: '.vote-bar.editable',
    handler: function () {
      jQuery(this)
        .hover(
          function() {
            jQuery('.stars-row.hovered', this).show();

          }, function() {

            if (!this._rating) {

              // Rating was not selected - clear all stars
              if (!this._previous) {
                this._previous = jQuery(this).children('.star-single');
              }
              this._previous.each(function(index) {
                  jQuery(this).removeClass('over');
              });

              jQuery('.stars-row.hovered', this).hide();

            } else {

              // Restore selection

              var rating = this._rating;

              jQuery('.stars-row.hovered', this).find('.star-single').each(function(index) {
                var j = parseInt(jQuery(this).attr('class').match(/\d+/));
                if (j > rating) {
                  jQuery(this).removeClass('over');
                } else {
                  jQuery(this).addClass('over');
                }
              });
            }
          });
    }
  }
);

CommonForm.elementControllers.push(
  {
    pattern: 'div.icon-help',
    handler: function () {

      var $tooltip = jQuery('#' + jQuery(this).attr('id') + '_tooltip');

      jQuery(this)
        .hover(
          function() {
            $tooltip.css({'display': 'block'});
          },
          function() {
            $tooltip.css({'display': 'none'});
          }
        );
    }
  }
);
