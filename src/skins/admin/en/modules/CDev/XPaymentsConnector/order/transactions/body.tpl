{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * List of X-Payments payments. Here are called as transactions. 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *
 * @ListChild (list="order.operations", weight="290")
 *}

<div IF="getXpcTransactions()" class="xpc-transactions line-3">
  <widget class="XLite\Module\CDev\XPaymentsConnector\View\ItemsList\Model\Order\Transactions" />
</div>

<br/>
