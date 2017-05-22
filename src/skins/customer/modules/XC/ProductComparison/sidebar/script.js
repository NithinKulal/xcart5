/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product comparison
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Widget
 */
function ProductComparisonView(base)
{
  this.callSupermethod('constructor', arguments);
  var o = this;
  core.bind(
    'updateProductComparison',
    function(event, data) {
      o.load();
    }
  );
}

extend(ProductComparisonView, ALoadable);

ProductComparisonView.autoload = function(){
  new ProductComparisonView(jQuery('.product-comparison'));
};

// No shade widget
ProductComparisonView.prototype.shadeWidget = false;

// Widget target
ProductComparisonView.prototype.widgetTarget = 'main';

// Widget class name
ProductComparisonView.prototype.widgetClass = '\\XLite\\Module\\XC\\ProductComparison\\View\\ProductComparison';

// Body handler is binded or not
ProductComparisonView.prototype.bodyHandlerBinded = false;

// Clear list
ProductComparisonView.prototype.clearList = function()
{
  this.load({action: 'clear'});

  return false;
}

// Postprocess widget
ProductComparisonView.prototype.postprocess = function(isSuccess)
{
  this.callSupermethod('postprocess', arguments);

  if (isSuccess) {
    var o = this;

    jQuery('.clear-list').click(
      function() {
        core.post(
          URLHandler.buildURL(
            {
              target: 'product_comparison',
              action: 'clear'
            }
          ),
          function(){},
          {
            target: 'product_comparison',
            action: 'clear'
          },
          {
            rpc: true
          }
        );
        jQuery('.compare-checkbox input').removeProp('checked');

        return false;
      }
    );

    jQuery('a.remove').click(
      function() {
        core.post(
          URLHandler.buildURL(
            {
              target: 'product_comparison',
              action: 'delete'
            }
          ),
          function(){},
          {
            target:     'product_comparison',
            action:     'delete',
            product_id: jQuery(this).data('id')
          },
          {
            rpc: true
          }
        );
        return false;
      }
    );

  }
}

core.autoload(ProductComparisonView);
