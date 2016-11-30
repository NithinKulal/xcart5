/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

window.getProductRepresentationFor = function(element) {
  var product = null;

  var itemView = null;
  var item = null;

  // on product details page
  if ($(element).hasClass('product-details')) {
    var product = $(element);
  } else {
    var product = $(element).closest('[class*="product-info-"]');
  }

  if (!product || product.length == 0) {
    // on product list page
    var product = $(element).closest('.product[class*="productid-"]');
    var products_list = product.closest('.items-list-products');
    var list_params = products_list.data('widget-arguments');

    switch(list_params.displayMode) {
      case 'grid':
      case 'list':
      var item = product.find('.product-photo img');
      break;

      case 'table':
      // var item = $(element).closest('tr').find('.product-sku');
      // var itemView = product.find('.product-table-representation');
      break;
    }
  } else {
    var item = product.find('.product-photo img');
  }

  return {
    element: item,
    view: itemView
  };
}

core.bind('autoload.before.product_filter_view', function() {
  if ($('.product-filter-placeholder').length > 0 && $('.product-filter').length > 0) {
    var filter = $('.product-filter').detach();

    $('.product-filter-placeholder').replaceWith(filter);
  }
});

core.bind('blocks.product_filter_view.before_submit', function() {
  if ($('.filters-zone').length > 0 && $('.filters-zone').hasClass('in')) {
    $('.filters-zone').removeClass('in');

    $('html, body').animate({
      scrollTop: parseInt($('.filters-mobile').closest('.items-list').offset().top - 60)
    }, 300);
  }
});

$('[data-toggle="collapse"]').each(function(index, el) {
  var $el = jQuery(el);
  var target = jQuery($el.data('target'));

  target.on('hide.bs.collapse', function () {
    $el.removeClass('shown');
  })
  target.on('hidden.bs.collapse', function () {
    $el.removeClass('shown');
  })
  target.on('show.bs.collapse', function () {
    $el.addClass('shown');
  })
  target.on('shown.bs.collapse', function () {
    $el.addClass('shown');
  })
});


jQuery(function() {
  $('.product-details-tabs ul.tabs').tabCollapse({
    tabsClass: 'hidden-xs',
    accordionClass: 'visible-xs'
  });
})
