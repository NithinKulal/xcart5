{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * iframe 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}
<div id="xpc">
  <iframe id="xpc_iframe_{getPaymentId()}" class="xpc_iframe" name="xpc_iframe" src="" data-src="{buildUrl(#checkout#,#checkout#,_ARRAY_(#xpc_iframe#^1))}"></iframe>
</div>
