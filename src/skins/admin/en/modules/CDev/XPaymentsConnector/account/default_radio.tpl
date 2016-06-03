{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * iframe 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}
{if:isDefaultCard(entity)}
  <input checked type="radio" name="default_card_id" value="{entity.getId()}" />
{else:}
  <input type="radio" name="default_card_id" value="{entity.getId()}" />
{end:}
