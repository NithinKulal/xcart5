{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * HTTPS Warning 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

{if:!isEnabledHTTPS()}
  <br/>

  <div class="alert alert-danger connection-status">
    <strong>{t(#Secure protocol is disabled!#)}</strong>
    {t(#Check#)} <a href="{buildUrl(#https_settings#)}" title="{t(#HTTPS settings#)}">{t(#HTTPS settings#)}</a>
  </div>
{end:}
