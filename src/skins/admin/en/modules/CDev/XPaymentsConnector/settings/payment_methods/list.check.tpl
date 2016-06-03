{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Test module 
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

{if:isSaveCards(entity)}
  <input type="checkbox" checked value="Y" name="save_cards[{entity.method_id}]">
{else:}
  <input type="checkbox" value="Y" name="save_cards[{entity.method_id}]">
{end:}

