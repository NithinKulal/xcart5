/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Orders list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
// function OrderDetails()
// {
//   jQuery(this.base).each(function (index, elem) {
//     var $elem = jQuery(elem);
//     var action = jQuery('#' + jQuery('.order-body-items-list', $elem).prop('id') + '-action');
//
//     jQuery('.order-body-items-list', $elem)
//       .on('show.bs.collapse', function () {
//         action.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
//       })
//       .on('hidden.bs.collapse', function () {
//         action.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
//       });
//   });
//
//   jQuery('i', this.base).eq(0).click();
// }
//
// OrderDetails.prototype.base = '.order-body-item';
//
// core.autoload('OrderDetails');

function OrdersListView(base)
{
  OrdersListView.superclass.constructor.apply(this, arguments);
}

extend(OrdersListView, ListView);

// Products list class
function OrdersListController(base)
{
  OrdersListController.superclass.constructor.apply(this, arguments);
}

extend(OrdersListController, ListsController);

OrdersListController.prototype.name = 'OrdersListController';

OrdersListController.prototype.findPattern += '.items-list-orders';

OrdersListController.prototype.getListView = function()
{
  return new OrdersListView(this.base);
};

OrdersListView.prototype.touchProcess = false;

OrdersListView.prototype.postprocess = function(isSuccess, initial)
{
  OrdersListView.superclass.postprocess.apply(this, arguments);

  jQuery('.order-body-item', this.base).each(function (index, elem) {
    var $elem = jQuery(elem);
    var action = jQuery('#' + jQuery('.order-body-items-list', $elem).prop('id') + '-action');

    jQuery('.order-body-items-list', $elem)
      .on('show.bs.collapse', function () {
        action.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
      })
      .on('hidden.bs.collapse', function () {
        action.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
      });
  });

  jQuery('i', this.base).eq(0).click();
};

OrdersListView.prototype.getEventNamespace = function () {
  return 'list.orders';
};

/**
 * Load product lists controller
 */
core.autoload(OrdersListController);
