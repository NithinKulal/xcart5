{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Deploy configuration 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<br/>

{if:isConfigured()}
  <widget template="modules/CDev/XPaymentsConnector/settings/connection/status.tpl">
{end:}

<h3>{t(#Use X-Payments configuration bundle#)}</h3>

<widget class="\XLite\Module\CDev\XPaymentsConnector\View\Form\DeployConfiguration" name="deploy" />

  <p>{t(#Copy the value of the Configuration field from X-Payments Online Store Details page, paste the string here and click Deploy. All the connection settings will be specified automatically.#):h}</p>

  <br/>

  <table class="settings-table">

    <tr>
      <td><widget class="\XLite\View\FormField\Input\Text" fieldName="deploy_configuration" fieldOnly="true" maxlength="false" /></td>
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td><widget class="\XLite\View\Button\Submit" label="{t(#Deploy#)}" style="main" /></td>
    </tr>

  </table>

<widget name="deploy" end />

<br/><br/>

<h3>{t(#Or fill in the settings manually#)}</h3>

<p>{t(#Specify the URL of your X-Payments installation. Copy and paste Store ID, Public key, Private key and Private key password from X-Payments Online Store Details page.#):h}</p>

<br/>
