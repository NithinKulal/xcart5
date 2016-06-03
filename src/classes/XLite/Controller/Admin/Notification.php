<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Notification controller
 */
class Notification extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('templatesDirectory');

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $notification = $this->getNotification();

        return $notification
            ? $notification->getName()
            : '';
    }

    /**
     * Returns description of current notification
     *
     * @return string
     */
    public function getDescription()
    {
        $notification = $this->getNotification();

        return $notification
            ? $notification->getDescription()
            : '';
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getNotification()) {
            $this->getModelForm()->performAction('modify');
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Notification';
    }

    /**
     * Returns notification
     *
     * @return \XLite\Model\Notification
     */
    protected function getNotification()
    {
        $id = \XLite\Core\Request::getInstance()->templatesDirectory;

        return $id
            ? \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id)
            : null;
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getNotification();
    }
}
