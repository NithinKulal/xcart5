<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\ItemsList\Module;

/**
 * Addons search and installation widget
 */
class Manage extends \XLite\View\ItemsList\Module\Manage implements \XLite\Base\IDecorator
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/CloudSearch/items_list/module/manage/style.css';

        return $list;
    }

    /**
     * Checks if this widget's module is a CloudSearch module
     *
     * @param \XLite\Model\Module $module
     *
     * @return bool
     */
    public function isCloudSearch(\XLite\Model\Module $module)
    {
        return $module->getActualName() == 'QSL\CloudSearch';
    }
}
