{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * iframe 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<div class="xpc-popup" >

  <div class="message">

    {if:isUnacceptedTemplateError(getMessage())}
      <p>{t(#Trying to use unaccepted template change.#)}</p>

      <p IF="isAdminUser()">{t(#You should accept the changes at the#)} <a href="{getDashboardLink()}" target="_blank">{t(#X-Payments Dashboard#)}</a></p>

      <p>{t(#Refer to on-line manual#)}: <a href="{getManualLink(505)}" target="_blank">{getManualTitle(505)}</a></p>

    {else:}

      <p>{t(getMessage())}</p>

      <p>{t(#If the problem still persists after refreshing the page please#)}
        <a href="{getContactUsLink()}" target="_blank">contact us</a>
        {t(#on the matter.#)}
      </p>

    {end:}

  </div>

  <br />

  <div class="buttons">
    <button type="button" class="btn  regular-button" onclick="javascript: {getButtonAction()};">{t(#Ok#)}</button>
  </div>

<br/>

