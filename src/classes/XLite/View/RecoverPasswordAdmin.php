<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Recover password dialog
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class RecoverPasswordAdmin extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'recover_password';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'unauthorized/style.less';
        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . (
            'recoverMessage' === \XLite\Core\Request::getInstance()->mode
                ? '/recover_message.twig'
                : '/recover_password.twig'
        );
    }

    /**
     * Defines directory where the templates and stylesheets are stored
     *
     * @return string
     */
    protected function getDir()
    {
        return 'password_recovery_admin';
    }
}
