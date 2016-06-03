<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * \XLite\View\Content
 *
 * @ListChild (list="body", zone="customer", weight="100")
 * @ListChild (list="body", zone="admin", weight="100")
 */
class Content extends \XLite\View\AView
{
    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * Controller content displayed flag
     *
     * @var boolean
     */
    protected static $controllerContentDisplayed = false;

    /**
     * display
     *
     * @param string $template Template file name OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        if (!static::$controllerContentDisplayed && isset(\XLite\View\Controller::$bodyContent)) {
            echo \XLite\View\Controller::$bodyContent;

            static::$controllerContentDisplayed = true;

        } else {
            parent::display($template);
        }
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getTitle()
    {
        if (!isset($this->title)) {
            $this->title = parent::getTitle();
        }

        return $this->title;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->isAJAXCenterRequest();
    }
}
