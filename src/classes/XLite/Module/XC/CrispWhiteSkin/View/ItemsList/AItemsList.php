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
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        $this->sortOrderModes[self::SORT_ORDER_ASC] = 'Low - High';
        $this->sortOrderModes[self::SORT_ORDER_DESC] = 'High - Low';

        parent::__construct($params);
    }

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
        return $this->sortByModes[$this->getSortBy()];
    }
}
