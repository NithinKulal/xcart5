/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * CloudSearch asynchronous JS code loader
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

(function () {
  var cs = document.createElement('script');
  cs.type = 'text/javascript';
  cs.async = true;
  cs.src = '//cdn-qualiteamsoftwar.netdna-ssl.com/cloud_search_xcart.js';

  var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(cs, s);
})();