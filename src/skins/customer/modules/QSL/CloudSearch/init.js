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
        EventHandlers: {OnPopupRender: []},
        requestData: {
            membership: cloudSearchData.membership,
            limits: {
                products: cloudSearchData.maxProducts
            }
        },
        positionPopupAt: function (searchInput) {
            var elem = searchInput.closest('.simple-search-box');

            return elem.length === 1 ? elem : searchInput;
        }
    };

    if (cloudSearchData.dynamicPricesEnabled) {
        var priceCache = {};

        window.Cloud_Search.EventHandlers.OnPopupRender.push(function (searchTerm, menu) {
            var popup = $(menu),
                products = $('.block-products dd', popup),
                prices = products.find('.price'),
                url;

            function populatePricesFromCache() {
                var pricesToRequest = [];

                prices.each(function () {
                    var e = $(this),
                        id = $(this).closest('dd').attr('data-id'),
                        price = priceCache[id];

                    if (typeof price !== 'undefined') {
                        if (price !== null) {
                            e.html(price);
                        }
                    } else {
                        pricesToRequest.push(id);
                    }
                });

                return pricesToRequest;
            }

            var pricesToRequest = populatePricesFromCache();

            if (pricesToRequest) {
                prices.each(function () {
                    var id = $(this).closest('dd').attr('data-id');

                    if (pricesToRequest.indexOf(id) !== -1) {
                        $(this).hide();
                    }
                });

                url = URLHandler.buildURL({
                    target: 'cloud_search_api',
                    action: 'get_prices',
                    ids: pricesToRequest.join(',')
                });

                core.get(url, function (data) {
                    var actualPrices = JSON.parse(data.responseText);

                    if (actualPrices) {
                        $.each(actualPrices, function (index) {
                            var price = actualPrices[index];

                            if (price !== null) {
                                priceCache[pricesToRequest[index]] = price;
                            }
                        });

                        populatePricesFromCache();
                    }

                    prices.show();
                });
            }
        });
    }
})(jQuery);
