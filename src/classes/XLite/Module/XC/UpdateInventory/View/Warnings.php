<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UpdateInventory\View;

/**
 * Warnings section widget
 */
class Warnings extends \XLite\View\Import\Warnings implements \XLite\Base\IDecorator
{
    /**
     * Return title
     *
     * @return string 
     */
    protected function getTitle()
    {
        return \XLite\Module\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY == $this->getImportTarget()
            ? static::t(
                'The script found {{number}} errors during update inventory',
                array(
                    'number' => \XLite\Core\Database::getRepo('XLite\Model\ImportLog')->count()
                )
            )
            : parent::getTitle();
    }
}
