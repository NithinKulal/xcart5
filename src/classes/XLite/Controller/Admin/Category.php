<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Category controller
 */
class Category extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'id', 'parent');

    /**
     * Category cache
     *
     * @var \XLite\Model\Category|null
     */
    protected $categoryObject = null;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return ($categoryName = $this->getCategoryName())
            ? static::t('Manage category (X)', array('category_name' => $categoryName))
            : static::t('No category defined');
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCategory();
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getCategory() ? $this->getCategory()->getName() : '';
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

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryId()
    {
        $id = \XLite\Core\Request::getInstance()->id;

        return $id && $id != $this->getRootCategoryId() ? $id : null;
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Category';
    }

}
