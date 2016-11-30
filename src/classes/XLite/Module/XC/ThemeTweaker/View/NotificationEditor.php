<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Theme tweaker template page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class NotificationEditor extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'notification_editor';

        return $list;
    }

    /**
     * Returns CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ThemeTweaker/notification_editor/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/body.twig';
    }

    /**
     * @return string
     */
    protected function getNotificationContent()
    {
        $mailer = new \XLite\View\Mailer();

        return $mailer->getNotificationEditableContent(
            $this->getDir(),
            $this->getData(),
            $this->getInterface()
        );
    }

    /**
     * @return mixed
     */
    protected function getDir()
    {
        return \XLite\Core\Request::getInstance()->templatesDirectory;
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return [
            'order' => \XLite\Module\XC\ThemeTweaker\Main::getDumpOrder(),
        ];
    }

    /**
     * @return string
     */
    protected function getInterface()
    {
        return \XLite\Core\Request::getInstance()->interface;
    }
}
