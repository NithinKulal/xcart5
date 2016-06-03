<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Subcategories list
 *
 * @ListChild (list="center.bottom", zone="customer", weight="100")
 */
class Subcategories extends \XLite\View\Dialog
{
    /**
     * Widget parameter names
     */
    const PARAM_DISPLAY_MODE = 'displayMode';
    const PARAM_ICON_MAX_WIDTH = 'iconWidth';
    const PARAM_ICON_MAX_HEIGHT = 'iconHeight';

    /**
     * Allowed display modes
     */
    const DISPLAY_MODE_LIST  = 'list';
    const DISPLAY_MODE_ICONS = 'icons';
    const DISPLAY_MODE_HIDE  = 'hide';

    /**
     * Display modes
     *
     * @var array
     */
    protected $displayModes = array(
        self::DISPLAY_MODE_LIST  => 'List',
        self::DISPLAY_MODE_ICONS => 'Icons',
        self::DISPLAY_MODE_HIDE  => 'Hide',
    );

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
     * Return list of required CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (!\XLite::isAdminZone()) {
            $list[] = 'common/grid-list.css';
        }

        return $list;
    }

    /**
     * Get image alternative text
     *
     * @param \XLite\Model\Image\Category\Image $image Image
     *
     * @return string
     */
    protected function getAlt($image)
    {
        return $image
            ? $image->getAlt() ?: $image->getCategory()->getName()
            : '';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'subcategories/' . $this->getDisplayMode();
    }

    /**
     * Returns default widget display mode
     *
     * @return string
     */
    protected function getDefaultDisplayMode()
    {
        return \XLite\Core\Config::getInstance()->General->subcategories_look ?: static::DISPLAY_MODE_ICONS;
    }

    /**
     * Get widget display mode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        if ('main' === \XLite\Core\Request::getInstance()->target) {
            $displayMode = $this->getRootCategoryDisplayMode();
        } else {
            $displayMode = $this->getParam(static::PARAM_DISPLAY_MODE);
        }

        return $displayMode;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->isCategoryVisible() && $this->hasSubcategories();
    }

    /**
     * Widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_DISPLAY_MODE => new \XLite\Model\WidgetParam\TypeSet(
                'Display mode',
                $this->getDefaultDisplayMode(),
                true,
                $this->displayModes
            ),
            static::PARAM_ICON_MAX_WIDTH => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon width',
                \XLite::getController()->getDefaultMaxImageSize(
                    true,
                    \XLite\Logic\ImageResize\Generator::MODEL_CATEGORY,
                    'Default'
                ),
                true
            ),
            static::PARAM_ICON_MAX_HEIGHT => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon height',
                \XLite::getController()->getDefaultMaxImageSize(
                    false,
                    \XLite\Logic\ImageResize\Generator::MODEL_CATEGORY,
                    'Default'
                ),
                true
            ),
        );
    }

    /**
     * Return the maximal icon width
     *
     * @return integer
     */
    protected function getIconWidth()
    {
        return $this->getParam(static::PARAM_ICON_MAX_WIDTH);
    }

    /**
     * Return the maximal icon height
     *
     * @return integer
     */
    protected function getIconHeight()
    {
        return $this->getParam(static::PARAM_ICON_MAX_HEIGHT);
    }

    /**
     * getColumnsCount
     *
     * @return integer
     */
    protected function getColumnsCount()
    {
        return 3;
    }

    /**
     * Return subcategories split into rows
     *
     * @return array
     */
    protected function getCategoryRows()
    {
        $rows = array_chunk($this->getSubcategories(), $this->getColumnsCount());
        $last = count($rows) - 1;
        $rows[$last] = array_pad($rows[$last], $this->getColumnsCount(), false);

        return $rows;
    }

    /**
     * Check for subcategories
     *
     * @return boolean
     */
    protected function hasSubcategories()
    {
        return $this->getCategory() ? $this->getCategory()->hasSubcategories() : false;
    }

    /**
     * Return subcategories
     *
     * @return array
     */
    protected function getSubcategories()
    {
        return $this->getCategory() ? $this->getCategory()->getSubcategories() : array();
    }

    /**
     * Check if the category is visible
     *
     * @return boolean
     */
    protected function isCategoryVisible()
    {
        return $this->getCategory() ? $this->getCategory()->isVisible() : false;
    }

    // {{{ Cache

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = $this->getCategoryId();

        $auth = \XLite\Core\Auth::getInstance();
        $list[] = ($auth->isLogged() && $auth->getProfile()->getMembership())
            ? $auth->getProfile()->getMembership()->getMembershipId()
            : '-';

        return $list;
    }

    /**
     * Get display mode of the front page
     *
     * @return string
     */
    protected function getRootCategoryDisplayMode()
    {
        $displayMode = $this->getCategory()->getRootCategoryLook();

        if (!$displayMode) {
            $displayMode = $this->getDefaultDisplayMode();
        }

        return $displayMode;
    }

    // }}}

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-subcategories';
    }
}
