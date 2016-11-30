/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * JS controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var showAdd2CartPopup = true;

function PopupButtonAdd2CartPopup()
{
  // Fallback
  var deboucedFallback = _.debounce(
    function(){
      showAdd2CartPopup = true;
    },
    5000
  );
  core.bind(
    'addToCartViaDrop',
    function(event, data) {
      if (!core.getCommentedData(jQuery('body'), 'a2cp_enable_for_dropping')) {
        showAdd2CartPopup = data.widget && data.widget.base.eq(0).hasClass('add-to-cart-popup');
      }

      deboucedFallback();
    }
  );

  core.bind(['updateCart', 'tryOpenAdd2CartPopup'], _.debounce(
      this.handleOpenPopup,
      500
  ));

  core.bind('addToCartViaClick', function(data) {
    showAdd2CartPopup = true;
    core.trigger('tryOpenAdd2CartPopup');
  });

  core.bind(
    'afterPopupPlace',
    function() {
      core.autoload(ProductsListController);
      popup.currentPopup.widget.addClass('add2cartpopup');
    }
  );

  PopupButtonAdd2CartPopup.superclass.constructor.apply(this, arguments);
}

decorate(
  'ProductDetailsView',
  'postprocessAdd2Cart',
  function (event, data) {
    arguments.callee.previousMethod.apply(this, arguments);

    core.trigger('tryOpenAdd2CartPopup');
  }
);

// Extend AController
extend(PopupButtonAdd2CartPopup, AController);

PopupButtonAdd2CartPopup.prototype.popupResult = null;

// Re-initialize products list controller
PopupButtonAdd2CartPopup.prototype.handleOpenPopup = function(event)
{
  setTimeout(
    function()
    {
      if (!showAdd2CartPopup) {
        return;
      }
      this.popupResult = !popup.load(
        URLHandler.buildURL({ target: 'add2_cart_popup' }),
        {
          dialogClass: 'add2cartpopup'
        }
      );
    },
    1
  );

  return this.popupResult;
};

// Autoloading new POPUP widget
core.autoload(PopupButtonAdd2CartPopup);
