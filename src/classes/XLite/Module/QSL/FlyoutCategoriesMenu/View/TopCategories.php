<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\FlyoutCategoriesMenu\View;

/**
 * Sidebar categories list
 */
abstract class TopCategories extends \XLite\View\TopCategories implements \XLite\Base\IDecorator
{
    const MAX_NESTING_DEPTH = 3;

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/FlyoutCategoriesMenu/';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_DISPLAY_MODE]->setValue(static::DISPLAY_MODE_TREE);
    }

    /**
     * @param array $categories
     *
     * @return array
     */
    protected function postprocessDTOs($categories)
    {
        $categories = parent::postprocessDTOs($categories);

        if (\XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_product_num) {
            foreach ($categories as $categoryDTO) {
                $tmpParent = isset($categories[$categoryDTO['parent_id']])
                    ? $categories[$categoryDTO['parent_id']]
                    : null;

                $productsCount = $categoryDTO['productsCount'];
                while ($tmpParent) {
                    $categories[$tmpParent['id']]['productsCount'] += $productsCount;
                    $tmpParent = isset($categories[$tmpParent['parent_id']])
                        ? $categories[$tmpParent['parent_id']]
                        : null;
                }
            }
        }

        return $categories;
    }

    /**
     * Assemble item CSS class name
     *
     * @param integer   $index    Item number
     * @param integer   $count    Items count
     * @param array     $category Current category
     *
     * @return string
     */
    protected function assembleItemClassName($index, $count, $category)
    {
        $classes = array();

        $active = $this->isActiveTrail($category['id']);

        if (!$category['hasSubcategories']) {
            $classes[] = 'leaf';
        }

        if (0 == $index) {
            $classes[] = 'first';
        }

        if (($count - 1) == $index) {
            $classes[] = 'last';
        }

        if ($active) {
            $classes[] = 'active-trail';
        }

        return implode(' ', $classes);
    }


    /**
     * Assemble item CSS class name
     *
     * @param integer   $index    Item number
     * @param integer   $count    Items count
     * @param array     $category Current category
     *
     * @return string
     */
    protected function assembleLinkClassName($index, $count, $category)
    {
        $classes = array();

        $classes[] = \XLite\Core\Request::getInstance()->category_id == $category['id']
            ? 'active'
            : '';

        $classes[] = $this->isWordWrapDisabled() ? 'no-wrap' : '';

        return implode(' ', $classes);
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-flyout-categories-menu';
    }

    /**
     * Check if display number of prducts
     *
     * @return boolean
     */
    protected function isShowProductNum()
    {
        return \XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_product_num;
    }

    /**
     * Check if display subcategory triangle
     *
     * @return boolean
     */
    protected function isShowTriangle()
    {
        return \XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_triangle;
    }

    /**
     * Check if word wrap disabled
     *
     * @return boolean
     */
    protected function isWordWrapDisabled()
    {
        return !\XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_word_wrap;
    }

    /**
     * Check if category depth doesnt exceed nesting level
     * @param  integer $depth Category depth
     *
     * @return boolean
     */
    protected function isNotDeep($depth)
    {
        return $depth < static::MAX_NESTING_DEPTH;
    }
}
