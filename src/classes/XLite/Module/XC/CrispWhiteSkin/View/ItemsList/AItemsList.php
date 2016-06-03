<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\ItemsList;

abstract class AItemsList extends \XLite\View\ItemsList\AItemsList implements \XLite\Base\IDecorator
{
    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/CrispWhiteSkin/items_list/items_list.js';

        return $list;
    }

    /**
     * @return string
     */
    protected function getSortByLabel()
    {
        $paramSortBy = $this->getParam(static::PARAM_SORT_BY);

        if (empty($paramSortBy)
            || !in_array($paramSortBy, array_keys($this->sortByModes))
        ) {
            $paramSortBy = $this->getSortByModeDefault();
        }

        return $this->sortByModes[$paramSortBy];
    }
}
