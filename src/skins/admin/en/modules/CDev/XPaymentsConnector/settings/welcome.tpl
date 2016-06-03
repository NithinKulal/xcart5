{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Welcome section. Used if connector is not configured. 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<div class="welcome-container">

  <widget template="{getDir()}/welcome/scary_note.tpl">  

  {if:isDisplaySteps()}

    <div class="connect-caption">
      {t(#Connect to X-Payments. Do 3 easy steps.#)}
    </div>

    <div class="steps">

      <widget template="{getDir()}/welcome/steps.top.tpl">

      <widget template="{getDir()}/welcome/steps.middle.tpl">

      <widget template="{getDir()}/welcome/steps.bottom.tpl">

    </div>

    <widget template="{getDir()}/welcome/video.tpl">

  {else:}

    <br /><br />

    <div style="text-align: center"> 
 
    <a href="{getAddPaymentConfLink()}" target="_blank" class="btn regular-button regular-main-button">{t(#Open X-Payments dashboard to configure#)}</a>

    </div>

  {end:}

</div>

