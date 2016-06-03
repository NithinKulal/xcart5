/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * CloudSearch initialization routine
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

(function ($) {
  var body = $('body'),
    cloudSearchData = core.getCommentedData(body, 'cloudSearch');

  window.Cloud_Search = {
    apiKey: cloudSearchData.apiKey,
    price_template: cloudSearchData.priceTemplate,
    selector: cloudSearchData.selector,
    lang: cloudSearchData.lng
  };
})(jQuery);
