{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Popup button (actually a link)
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}
<a href="javascript: void(0);" class="{getClass()}">
{displayCommentedData(getURLParams())}
<span>{t(getButtonContent())}</span>
</a>
