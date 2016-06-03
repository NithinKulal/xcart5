<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Notification help
 */
class NotificationHelp extends \XLite\View\AView
{
    /**
     * Add CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'notification/help.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'notification/help.twig';
    }

    /**
     * Return all variables
     *
     * @return array
     */
    protected function getVariables()
    {
        return \XLite\Core\Mailer::getInstance()->getAllVariables();
    }
}
