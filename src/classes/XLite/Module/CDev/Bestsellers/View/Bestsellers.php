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
    const SIDEBAR_LIST = 'sidebar.first';
    const SIDEBAR_SINGLE = 'sidebar.single';
    const CENTER_LIST = 'center.bottom';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = 'main';
        $result[] = 'category';

        return $result;
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

        $this->widgetParams[static::PARAM_WIDGET_TYPE]->setValue(static::WIDGET_TYPE_CENTER);

        $this->widgetParams[static::PARAM_DISPLAY_MODE]->setValue(static::DISPLAY_MODE_GRID);
        $this->widgetParams[static::PARAM_GRID_COLUMNS]->setValue(3);

        unset($this->widgetParams[static::PARAM_SHOW_DISPLAY_MODE_SELECTOR]);
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return mixed
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if (!isset($this->bestsellProducts)) {
            $limit = \XLite\Core\Config::getInstance()->CDev->Bestsellers->number_of_bestsellers;

            $this->bestsellProducts = \XLite\Core\Database::getRepo('XLite\Model\Product')
                ->findBestsellers(
                    $cnd,
                    (int)$limit,
                    $this->getRootId()
                );
        }

        $result = true === $countOnly
            ? count($this->bestsellProducts)
            : $this->bestsellProducts;

        return $result;
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

        if (
            $template == $this->getDefaultTemplate()
            && static::WIDGET_TYPE_SIDEBAR == $this->getParam(static::PARAM_WIDGET_TYPE)
        ) {
            $template = 'common/sidebar_box.twig';
        }

        return $template;
    }
}
