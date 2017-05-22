<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * \XLite\Controller\Customer\Main
 */
class Main extends \XLite\Controller\Customer\Category
{
    /**
     * Controller parameters list
     *
     * @var array
     */
    protected $params = array('target');

    /**
     * Preprocessor for no-action ren
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (!\XLite\Core\Request::getInstance()->isAJAX()) {
            \XLite\Core\Session::getInstance()->productListURL = $this->getURL();
        }
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->getCategory() && $this->getRootCategoryId() == $this->getCategory()->getCategoryId();
    }

    protected function isNeedToRedirectToMain()
    {
        return false;
    }
}
