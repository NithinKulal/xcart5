<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Bestsellers\View;

/**
 * Bestsellers widget
 *
 * @ListChild (list="center.bottom", zone="customer", weight="400")
 */
class Bestsellers extends \XLite\Module\CDev\Bestsellers\View\ABestsellers
{
    /**
     * List names where the Bestsellers block is located
     */
    const SIDEBAR_LIST   = 'sidebar.first';
    const SIDEBAR_SINGLE = 'sidebar.single';
    const CENTER_LIST    = 'center.bottom';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'main';
        $result[] = 'category';

        return $result;
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Model\Repo\Product::P_CATEGORY_ID => static::PARAM_CATEGORY_ID,
        ];
    }

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if (!$this->isSidebar()) {
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR]->setValue(false);
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_COUNT]
                ->setValue(\XLite\Core\Config::getInstance()->CDev->Bestsellers->number_of_bestsellers);
        }
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        unset($this->widgetParams[static::PARAM_SHOW_DISPLAY_MODE_SELECTOR]);
    }

    /**
     * Return default display mode from settings
     */
    protected function getDefaultDisplayMode()
    {
        return static::DISPLAY_MODE_GRID;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Infinity';
    }

    /**
     * Get limit condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getLimitCondition()
    {
        $cnd = $this->getSearchCondition();

        $cnd->{\XLite\Model\Repo\ARepo::P_LIMIT} = [
            0,
            \XLite\Core\Config::getInstance()->CDev->Bestsellers->number_of_bestsellers,
        ];

        return $cnd;
    }

    /**
     * getSidebarMaxItems
     *
     * @return integer
     */
    protected function getSidebarMaxItems()
    {
        return \XLite\Core\Config::getInstance()->CDev->Bestsellers->number_of_bestsellers;
    }

    /**
     * Return template of Bestseller widget. It depends on widget type:
     * SIDEBAR/CENTER and so on.
     *
     * @return string
     */
    protected function getTemplate()
    {
        $template = parent::getTemplate();
        if ($template === $this->getDefaultTemplate()
            && static::WIDGET_TYPE_SIDEBAR === $this->getParam(static::PARAM_WIDGET_TYPE)
        ) {
            $template = 'common/sidebar_box.twig';
        }

        return $template;
    }
}
