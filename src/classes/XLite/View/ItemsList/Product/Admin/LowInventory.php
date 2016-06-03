<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product\Admin;

/**
 * LowInventory
 */
class LowInventory extends \XLite\View\ItemsList\Product\Admin\AAdmin
{
    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Products with low inventory';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Admin\Product';
    }

    /**
     * getDisplayStyle
     *
     * @return string
     */
    protected function getDisplayStyle()
    {
        return 'brief';
    }

    /**
     * Do not display 'Products with low inventory' block if low-limit-products list is empty
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && 0 < $this->getData($this->getSearchCondition(), true);
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return true;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Product::P_INVENTORY} = \XLite\Model\Repo\Product::INV_LOW;

        return $result;
    }

    /**
     * Define view list
     *
     * @param string $list List name
     *
     * @return array
     */
    protected function defineViewList($list)
    {
        $result = parent::defineViewList($list);

        if ($this->getListName() . '.footer' === $list) {
            $result[] = $this->getWidget(array('label' => 'Update'), '\XLite\View\Button\Submit');
        }

        return $result;
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Product')->search($cnd, $countOnly);
    }
}
