<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Category products controller
 */
class CategoryProducts extends \XLite\Controller\Admin\ProductList
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() && $this->getCategoryId();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->checkACL()
            ? static::t('Manage category (X)', array('category_name' => $this->getCategoryName()))
            : '';
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryName()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')
            ->find($this->getCategoryId())->getName();
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategory()
    {
        if (is_null($this->category)) {
            $this->category = \XLite\Core\Database::getRepo('XLite\Model\Category')
            ->find($this->getCategoryId());
        }

        return $this->category;
    }
}
