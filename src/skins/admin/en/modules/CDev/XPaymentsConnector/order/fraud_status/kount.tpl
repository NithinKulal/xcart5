{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Kount results 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="order", weight="1000")
 *}

<div IF="getKountData()" class="kount-result">

  <a name="fraud-info-kount"></a>

  <h2>{t(#KOUNT Antifraud screening result#)}</h2>

  {if:getKountErrors()}
    {foreach:getKountErrors(),error}
      <div class="alert alert-danger">
        <strong>{t(#Error#)}!</strong>
          {error:h}
      </div>
    {end:}
  {end:}

  <p class="lead" IF="getKountMessage()">
    {getKountMessage():h}. {t(#Score#)}:
    <span class="lead {getKountScoreClass()}">
      {getKountScore()}
    </span>
  </p>

  <p IF="getKountTransactionId()">{t(#Transaction ID#)}: {getKountTransactionId()}</p>

  {if:getKountRules()}

    <h3>{t(#Rules triggered#)}:</h3>

    <ul class="kount-result-lines">
      {foreach:getKountRules(),title,value}
        <li>{value:h}</li>
      {end:}
    </ul>

  {end:}

</div>

<br/>
