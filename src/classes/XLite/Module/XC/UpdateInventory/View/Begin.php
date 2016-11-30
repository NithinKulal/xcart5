<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UpdateInventory\View;

/**
 * Begin import section widget
 */
class Begin extends \XLite\View\Import\Begin implements \XLite\Base\IDecorator
{
    /**
     * Return samples URL
     *
     * @return string
     */
    protected function getSamplesURL()
    {
        return $this->isUpdateQty()
            ? 'http://kb.x-cart.com/en/modules/update_inventory.html'
            : parent::getSamplesURL();
    }

    /**
     * Return samples URL text
     *
     * @return string
     */
    protected function getSamplesURLText()
    {
        return $this->isUpdateQty()
            ? static::t('Update quantity import guide')
            : parent::getSamplesURLText();
    }

    /**
     * Return true if current widget used in 'update_inventory' page
     *
     * @return boolean
     */
    protected function isUpdateQty()
    {
        return \XLite\Module\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY == $this->getImportTarget();
    }
}
