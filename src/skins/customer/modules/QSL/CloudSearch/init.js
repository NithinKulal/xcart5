/**
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function ($) {
  var body = $('body'),
    cloudSearchData = core.getCommentedData(body, 'cloudSearch');

  window.Cloud_Search = {
    apiKey: cloudSearchData.apiKey,
    price_template: cloudSearchData.priceTemplate,
    selector: cloudSearchData.selector,
    lang: cloudSearchData.lng,
    EventHandlers: {
      OnPopupRender: [
        function () {
          var popup = $('#instant_search_menu'),
            products = $('.block-products dd', popup),
            prices = products.find('.price'),
            pids = [],
            url;

          prices.hide();

          products.each(function () {
            pids.push($(this).attr('data-id'));
          });

          if (pids) {
            url = URLHandler.buildURL({target: 'cloud_search_api', action: 'get_prices', ids: pids.join(',')});
            
            core.get(url, function (data) {
              var actualPrices = JSON.parse(data.responseText);
              if (actualPrices) {
                prices.each(function (index) {
                  var e = $(this),
                    price = actualPrices[index];

                  price !== null && e.html(price);
                });
              }
              prices.show();
            });
          }
        }
      ]
    }
  };
})(jQuery);
