<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View;

/**
 * New arrivals block widget
 *
 * @ListChild (list="sidebar.single", zone="customer", weight="160")
 * @ListChild (list="sidebar.second", zone="customer", weight="110")
 */
class NewArrivals extends \XLite\Module\CDev\ProductAdvisor\View\ANewArrivals
{
    /**
     * Widget parameter
     */
    const PARAM_MAX_ITEMS_TO_DISPLAY = 'maxItemsToDisplay';

    /**
     * Flag: count all existing new arrival products or only displayed in widget
     *
     * @var boolean
     */
    protected $countAllNewProducts = false;

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

        unset($this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR]);
        unset($this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE]);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_USE_NODE => new \XLite\Model\WidgetParam\TypeCheckbox(
                'Show products only for current category',
                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->na_from_current_category,
                true
            ),
            self::PARAM_ROOT_ID => new \XLite\Model\WidgetParam\ObjectId\Category(
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
        );

        $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_SIDEBAR);

        unset($this->widgetParams[self::PARAM_SHOW_DISPLAY_MODE_SELECTOR]);
        unset($this->widgetParams[self::PARAM_SHOW_SORT_BY_SELECTOR]);
    }
    
    /**
     * Returns search products conditions
     *
     * @param \XLite\Core\CommonCell $cnd Initial search conditions
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchConditions(\XLite\Core\CommonCell $cnd)
    {
        $cnd = parent::getSearchConditions($cnd);

        $categoryId = $this->getRootId();

        if (!$this->countAllNewProducts && $categoryId) {
            $cnd->{\XLite\Model\Repo\Product::P_CATEGORY_ID} = $categoryId;
            $cnd->{\XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS} = true;
        }

        if (!$this->countAllNewProducts && $this->getMaxItemsCount()) {
            $cnd->{\XLite\Model\Repo\Product::P_LIMIT} = array(
                0,
                $this->getMaxItemsCount()
            );
        }

        return $cnd;
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
            ? intval(\XLite\Core\Request::getInstance()->category_id)
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

        if ($template == $this->getDefaultTemplate()
            && self::WIDGET_TYPE_SIDEBAR == $this->getParam(self::PARAM_WIDGET_TYPE)
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
        return static::getWidgetTarget() != \XLite\Core\Request::getInstance()->target
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
        $this->countAllNewProducts = true;

        $result = ($this->getData(new \XLite\Core\CommonCell, true) > $this->getMaxItemsCount());

        $this->countAllNewProducts = false;

        return $result;
    }

    /**
     * Get 'More...' link URL for New arrivals list
     *
     * @return string
     */
    protected function getMoreLinkURL()
    {
        return $this->buildURL(self::WIDGET_TARGET_NEW_ARRIVALS);
    }

    /**
     * Get 'More...' link text for New arrivals list
     *
     * @return string
     */
    protected function getMoreLinkText()
    {
        return \XLite\Core\Translation::getInstance()->translate('All newest products');
    }
}
