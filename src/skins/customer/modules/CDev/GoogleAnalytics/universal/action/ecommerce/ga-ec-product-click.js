/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * upadte cart event
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('googleAnalytics/eCommerceProductClickEvent', ['googleAnalytics/eCommerceCoreEvent', 'js/underscore'], function (eCommerceCoreEvent, _) {
  eCommerceProductClickEvent = eCommerceCoreEvent.extend({

    constructor: function () {
      eCommerceProductClickEvent.superclass.constructor.apply(this, arguments);

      var productLinks = jQuery('.products a.product-thumbnail, .products a.fn, .products a.url');

      var self = this;

      productLinks.click(function (event) {
        var dataOwner = jQuery('*[data-ga-ec-action]', jQuery(event.currentTarget).parents('.product'));

        if (dataOwner.length) {
          self.onProductDetailsClick(
              dataOwner.data('ga-ec-action').data,
              function() {
                var href = jQuery(event.currentTarget).attr('href');
                if (!href.match(/^http|^\/\//)) {
                  var bases = document.getElementsByTagName('base');

                  href = (bases.length > 0 ? bases[0].href : '') + href;
                }

                document.location = href;
              }
          );

          return !ga.loaded;
        }

        return true;
      });

      jQuery('.products .add-to-cart').not('.link').each(
          function (index, elem) {
            jQuery(elem).click(function(event) {
              var dataOwner = jQuery('*[data-ga-ec-action]', jQuery(event.currentTarget).parents('.product-cell'));

              self.onProductAddClick(dataOwner.data('ga-ec-action').data)
            });
          }
      );

      jQuery('.products .quicklook-link').each(
          function (index, elem) {
            jQuery(elem).click(function(event) {
              var dataOwner = jQuery('*[data-ga-ec-action]', jQuery(event.currentTarget).parents('.product-cell'));

              self.onProductQuickViewClick(dataOwner.data('ga-ec-action').data)
              core.trigger('ga-ec-details-shown', {
                data: dataOwner.data('ga-ec-action').data,
                message: 'Details shown [Quick view]'
              });
            });
          }
      );
    },

    onProductDetailsClick: function (data, callback) {
      ga('ec:addProduct', data);

      ga('ec:setAction', 'click', {
        list: data.list
      });

      ga('send', 'event', 'UX', 'click details', data.list, {
        hitCallback: callback
      });
    },

    onProductAddClick: function (data) {
      ga('ec:addProduct', data);

      ga('ec:setAction', 'click', {
        list: data.list
      });

      ga('send', 'event', 'UX', 'click add on list', data.list);
    },

    onProductQuickViewClick: function (data) {
      ga('ec:addProduct', data);

      ga('ec:setAction', 'click', {
        list: data.list
      });

      ga('send', 'event', 'UX', 'click on quickview', data.list);
    },

  });

  eCommerceProductClickEvent.instance = new eCommerceProductClickEvent();

  return eCommerceProductClickEvent;
});