<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Attribute page view
 *
 * @ListChild (list="admin.center", zone="admin", weight="0")
 */
class CategoryFormattedPath extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            array(
                'category',
                'categories',
                'category_products',
            )
        );
    }

    /**
     * Check if the widget is visible
     *
     * @return boolean
     */
    public function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return the CSS files for the widget
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'category_formatted_path/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'category_formatted_path/body.twig';
    }

    /**
     * Get category
     *
     * @return \XLite\Model\Category
     */
    protected function getCategory()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')->find(intval(\XLite\Core\Request::getInstance()->id));
    }

    /**
     * Check if category is last
     *
     * @return array
     */
    protected function isCurrentCategory(\XLite\Model\Category $category)
    {
        return $this->getCategory() === $category;
    }

    /**
     * Get path of current category
     *
     * @return array
     */
    protected function getCategoryPath()
    {
        return $this->getCategory()->getPath();
    }

}