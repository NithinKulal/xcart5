<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Front page controller
 */
class FrontPage extends \XLite\Controller\Admin\Category
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target');

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->getRootCategoryId();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Front page');
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\FrontPage';
    }

}
