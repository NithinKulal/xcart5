<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to category section
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Category extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'categories';
        if (\XLite\Core\Request::getInstance()->id) {
            $list[] = 'category';
            $list[] = 'category_products';
        }

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'category' => [
                'weight'   => 100,
                'title'    => static::t('Category info'),
                'template' => 'category/body.twig',
            ],
            'categories' => [
                'weight'   => 200,
                'title'    => static::t('Subcategories'),
                'widget'    => '\XLite\View\ItemsList\Model\Category',
            ],
            'category_products' => [
                'weight'   => 300,
                'title'    => static::t('Products'),
                'widget'    => '\XLite\View\ItemsList\Model\Product\Admin\CategoryProducts',
            ],
        ];
    }

    /**
     * Sorting the tabs according their weight
     *
     * @return array
     */
    protected function prepareTabs()
    {
        if (!\XLite\Core\Request::getInstance()->id
            && !\XLite\Core\Request::getInstance()->parent
        ) {
            // Front page
            $this->tabs['categories']['title'] = static::t('Main categories');
            unset($this->tabs['category'], $this->tabs['category_products']);

        } elseif (!\XLite\Core\Request::getInstance()->id) {
            // New category
            unset($this->tabs['categories'], $this->tabs['category_products']);
        }

        return parent::prepareTabs();
    }

        /**
     * Returns an URL to a tab
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        return $this->buildURL($target, '', ['id' => \XLite\Core\Request::getInstance()->id]);
    }

    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return boolean
     */
    protected function isTabsNavigationVisible()
    {
        $visible = parent::isTabsNavigationVisible();

        if (!\XLite\Core\Request::getInstance()->id
            && !\XLite\Core\Request::getInstance()->parent
        ) {
            $visible = false;
        }

        return $visible;
    }
}
