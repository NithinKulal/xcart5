<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UpdateInventory\View;

/**
 * Update inventory page main widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class UpdateInventory extends \XLite\View\Page\Admin\Import
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = array(\XLite\Module\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY);

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/UpdateInventory/style.css';

        return $list;
    }
}
