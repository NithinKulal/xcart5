<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UpdateInventory\View\Form;

/**
 * Import form widget
 */
class Import extends \XLite\View\Form\Import implements \XLite\Base\IDecorator
{
    /**
     * Get default target
     *
     * @return string
     */
     protected function getDefaultTarget()
     {
         return \XLite\Module\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY == $this->getTarget()
             ? \XLite\Module\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY
             : parent::getDefaultTarget();
     }
}
