<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Countries management page controller
 */
class Confirm extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Confirmation');
    }

    /**
     * Do action
     *
     * @return void
     */
    protected function doActionConfirmWithPassword()
    {
        $password = \XLite\Core\Request::getInstance()->password;

        $result = null !== $password
            && \XLite\Core\Auth::comparePassword(
                \XLite\Core\Auth::getInstance()->getProfile()->getPassword(),
                $password
            );

        if (!$result) {
            \XLite\Core\TopMessage::addError('Incorrect password. Please try again.');
        }

        \XLite\Core\Event::passwordConfirmed(array('result' => $result));
    }
}
