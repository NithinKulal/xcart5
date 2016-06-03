{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Payment methods 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<br/><br/>

<div class="not-configured">

  <p>
    {t(#Note: No payment methods here because the module is not configured. Please finalize settings at #):h}
    <b><a href="{buildUrl(#module#,##,_ARRAY_(#moduleId#^module.getModuleID(),#section#^#connection#))}">{t(#Connection#)}</a></b>
    {t(#tab and get back here to configure methods.#):h}
  </p>

</div>
