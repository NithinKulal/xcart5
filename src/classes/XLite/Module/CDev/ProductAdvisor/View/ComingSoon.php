<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View;

/**
 * Coming soon block widget
 *
 * @ListChild (list="sidebar.single", zone="customer", weight="170")
 * @ListChild (list="sidebar.second", zone="customer", weight="120")
 */
class ComingSoon extends \XLite\Module\CDev\ProductAdvisor\View\AComingSoon
{
    /**
     * Widget parameter
     */
    const PARAM_MAX_ITEMS_TO_DISPLAY = 'maxItemsToDisplay';

    /**
     * Flag: count all existing products or only displayed in widget
     *
     * @var boolean
     */
    protected $countAllComingSoonProducts = false;

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

        unset(
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR],
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE]
        );
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_USE_NODE             => new \XLite\Model\WidgetParam\TypeCheckbox(
                'Show products only for current category',
                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_from_current_category,
                true
            ),
            self::PARAM_ROOT_ID              => new \XLite\Model\WidgetParam\ObjectId\Category(
                'Root category Id',
                0,
                true,
                true
            ),
            self::PARAM_MAX_ITEMS_TO_DISPLAY => new \XLite\Model\WidgetParam\TypeInt(
                'Maximum products to display',
                $this->getMaxCountInBlock(),
                true,
                true
            ),
        ];

        $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_SIDEBAR);

        unset(
            $this->widgetParams[self::PARAM_SHOW_DISPLAY_MODE_SELECTOR],
            $this->widgetParams[self::PARAM_SHOW_SORT_BY_SELECTOR]
        );
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);

        $categoryId = $this->getRootId();
        if ($this->countAllComingSoonProducts || !$categoryId) {
            unset(
                $searchCase->{\XLite\Model\Repo\Product::P_CATEGORY_ID},
                $searchCase->{\XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS}
            );

        } elseif ($categoryId) {
            $searchCase->{\XLite\Model\Repo\Product::P_CATEGORY_ID}       = $categoryId;
            $searchCase->{\XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS} = true;
        }

        return $searchCase;
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getLimitCondition()
    {
        $cnd = $this->getSearchCondition();
        if ($this->countAllComingSoonProducts) {

            return $this->getPager()->getLimitCondition(0, $this->getMaxItemsCount(), $cnd);
        }

        return $cnd;
    }

    /**
     * getSidebarMaxItems
     *
     * @return integer
     */
    protected function getSidebarMaxItems()
    {
        return $this->getMaxItemsCount();
    }

    /**
     * Returns maximum allowed items count
     *
     * @return integer
     */
    protected function getMaxItemsCount()
    {
        return $this->getParam(self::PARAM_MAX_ITEMS_TO_DISPLAY) ?: $this->getMaxCountInBlock();
    }

    /**
     * Return category Id to use
     *
     * @return integer
     */
    protected function getRootId()
    {
        return $this->getParam(self::PARAM_USE_NODE)
            ? (int) \XLite\Core\Request::getInstance()->category_id
            : $this->getParam(self::PARAM_ROOT_ID);
    }

    /**
     * Return template of New arrivals widget. It depends on widget type:
     * SIDEBAR/CENTER and so on.
     *
     * @return string
     */
    protected function getTemplate()
    {
        $template = parent::getTemplate();
        if ($template === $this->getDefaultTemplate()
            && self::WIDGET_TYPE_SIDEBAR === $this->getWidgetType()
        ) {
            $template = self::TEMPLATE_SIDEBAR;
        }

        return $template;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return static::getWidgetTarget() !== \XLite\Core\Request::getInstance()->target
        && parent::isVisible()
        && 0 < $this->getItemsCount();
    }

    /**
     * Check status of 'More...' link for New arrivals list
     *
     * @return boolean
     */
    protected function isShowMoreLink()
    {
        $this->countAllComingSoonProducts = true;

        $result = $this->getItemsCount() > $this->getMaxItemsCount();

        $this->countAllComingSoonProducts = false;

        return $result;
    }

    /**
     * Get 'More...' link URL for New arrivals list
     *
     * @return string
     */
    protected function getMoreLinkURL()
    {
        return $this->buildURL(self::WIDGET_TARGET_COMING_SOON);
    }

    /**
     * Get 'More...' link text for New arrivals list
     *
     * @return string
     */
    protected function getMoreLinkText()
    {
        return \XLite\Core\Translation::getInstance()->translate('All upcoming products');
    }
}
