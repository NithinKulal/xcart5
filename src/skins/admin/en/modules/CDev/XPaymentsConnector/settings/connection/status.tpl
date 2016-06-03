{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Connection status
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<br />

{if:isConnected()}
  <div class="alert alert-success connection-status">
      <strong>{t(#Connected!#)}</strong>
      {t(#Connection with X-Payments is OK.#)}
  </div>
{else:}
  <div class="alert alert-danger connection-status">
      <strong>{t(#Connection failed!#)}</strong>
      {t(#Check the settings.#)}
  </div>
{end:}

<br />
